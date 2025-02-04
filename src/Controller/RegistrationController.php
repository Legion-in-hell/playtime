<?php

namespace App\Controller;

use App\Entity\StandardUser;
use App\Entity\SportCompany;
use App\Form\StandardUserRegistrationFormType;
use App\Form\SportCompanyRegistrationFormType;
use App\Security\EmailVerifier;
use App\Service\GeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function chooseRegistrationType(): Response
    {
        return $this->render('registration/choose_type.html.twig');
    }

    #[Route('/register/user', name: 'app_register_user')]
    public function registerUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new StandardUser();
        $form = $this->createForm(StandardUserRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            if ($request->query->has('reservation')) {
                parse_str($request->query->get('reservation'), $reservationData);

                $updatedQuery = http_build_query([
                    'reservation' => [
                        'service' => $reservationData['reservation']['service'],
                        'terrain' => $reservationData['reservation']['terrain'],
                        'date' => $reservationData['reservation']['date'],
                        'time' => $reservationData['reservation']['time'],
                        'company' => $reservationData['reservation']['company'],
                        'user' => $user->getId(),
                    ],
                ]);

                return $this->redirectToRoute('create_reservation', ['reservation' => $updatedQuery]);
            }

            // $this->sendVerificationEmail($user);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register_user.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
    #[Route('/register/company', name: 'app_register_company')]
    public function registerCompany(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, GeocodingService $geocodingService): Response
    {
        $company = new SportCompany();
        $form = $this->createForm(SportCompanyRegistrationFormType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adress = $company->getAddress() . ' ' . $company->getPostalCode() . ' ' . $company->getCity();
            $coordinates = $geocodingService->geocodeAdress($adress);

            if ($coordinates) {
                $company->setLatitude($coordinates['latitude']);
                $company->setLongitude($coordinates['longitude']);
            }

            $company->setRoles(['ROLE_COMPANY']);
            $company->setPassword(
                $userPasswordHasher->hashPassword(
                    $company,
                    $form->get('plainPassword')->getData()
                )
            );

            /*foreach ($company->getImages() as $image) {
                $image->setSportCompany($company);
                $entityManager->persist($image);
            }*/

            $entityManager->persist($company);
            $entityManager->flush();

            // $this->sendVerificationEmail($company);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register_company.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function sendVerificationEmail($user): void
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('registration@playtime.test', 'Playtime Account Manager'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register_user');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('home');
    }
}