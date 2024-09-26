<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Entity\SportCompany;
use App\Form\SportCompanyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reservation;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CompanyDashboardController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'company_dashboard')]
    #[IsGranted('ROLE_COMPANY')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof SportCompany) {
            throw $this->createAccessDeniedException('User not found or not a SportCompany.');
        }

        $reservations = $this->entityManager->getRepository(Schedule::class)->findBy(['sportCompany' => $user]);

        return $this->render('dashboard/company_dashboard.html.twig', [
            'company' => $user,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/dashboard/company/update', name: 'company_update')]
    #[IsGranted('ROLE_COMPANY')]
    public function updateCompany(Request $request): Response
    {
        $company = $this->getUser();
        if (!$company instanceof SportCompany) {
            throw $this->createAccessDeniedException('User not found or not a SportCompany.');
        }

        $form = $this->createForm(SportCompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Les informations de votre entreprise ont été mises à jour avec succès.');
            return $this->redirectToRoute('company_dashboard');
        }

        return $this->render('dashboard/company_update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/services', name: 'company_services')]
    #[IsGranted('ROLE_COMPANY')]
    public function manageServices(): Response
    {
        $company = $this->getUser();
        if (!$company instanceof SportCompany) {
            throw $this->createAccessDeniedException('User not found or not a SportCompany.');
        }

        return $this->render('dashboard/company_services.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/dashboard/schedule', name: 'company_schedule')]
    #[IsGranted('ROLE_COMPANY')]
    public function manageSchedule(): Response
    {
        $company = $this->getUser();
        if (!$company instanceof SportCompany) {
            throw $this->createAccessDeniedException('User not found or not a SportCompany.');
        }

        return $this->render('dashboard/company_schedule.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/dashboard/reservations', name: 'company_reservations')]
    #[IsGranted('ROLE_COMPANY')]
    public function showReservations(): Response
    {
        $company = $this->getUser();
        if (!$company instanceof SportCompany) {
            throw $this->createAccessDeniedException('User not found or not a SportCompany.');
        }

        $reservations = $this->entityManager->getRepository(Schedule::class)->findBy(['sportCompany' => $company]);

        return $this->render('dashboard/company_reservations.html.twig', [
            'company' => $company,
            'reservations' => $reservations,
        ]);
    }
}