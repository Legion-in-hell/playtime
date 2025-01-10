<?php

namespace App\Controller;
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\SportCompany;
use App\Entity\Terrain;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Service\ReservationValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormError;
use App\Entity\StandardUser;
use function League\Uri\parse;

class ReservationController extends AbstractController
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    #[Route('/reservation/new/{id}', name: 'make_reservation')]
    public function new(Request $request, SportCompany $company, ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();

        if ($user && in_array('ROLE_COMPANY', $user->getRoles())) {
            return $this->redirectToRoute('home');
        }

        if($request->isMethod('POST')){
            $reservationData = $request->request->all();

            if (!isset($reservationData['reservation']['service'], $reservationData['reservation']['terrain'], $reservationData['reservation']['date'], $reservationData['reservation']['time'])) {
                $this->addFlash('error', 'Réservation incorrecte, veuillez recommencer.');
                return $this->redirectToRoute('make_reservation');
            }

            $params = http_build_query([
                'reservation' => [
                    'service' => $reservationData['reservation']['service'],
                    'terrain' => $reservationData['reservation']['terrain'],
                    'date' => $reservationData['reservation']['date'],
                    'time' => $reservationData['reservation']['time'],
                    'company' => $company->getId()
                ],
            ]);

            if(!$this->getUser()){
                return $this->redirectToRoute('app_register_user', ['reservation' => $params]);
            }

            return $this->redirectToRoute('create_reservation', ['reservation' => $params]);
        }

        $reservation = new Reservation();
        $openingHours = $this->getOpeningHours($company);

        $form = $this->createForm(ReservationType::class, $reservation, [
            'company' => $company,
        ]);

        $form->handleRequest($request);

        $allReservations = $reservationRepository->findBy(['sportCompany' => $company]);
        $reservedTimes = [];

        foreach ($allReservations as $existingReservation) {
            $date = $existingReservation->getDate()->format('Y-m-d');
            $time = $existingReservation->getTime()->format('H:i');

            if (!isset($reservedTimes[$date])) {
                $reservedTimes[$date] = [];
            }

            $reservedTimes[$date][] = $time;
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
            'company' => $company,
            'openingHours' => json_encode($openingHours),
            'reservedTimes' => json_encode($reservedTimes),
        ]);
    }

    #[Route('/reservation/create', name: 'create_reservation')]
    public function createReservation(Request $request, EntityManagerInterface $entityManager, ReservationValidator $validator): Response
    {
        $user = $this->getUser();
        parse_str($request->query->get('reservation'), $reservationData);

        if (!$user && $reservationData['reservation']['user']) {
            $userId = $reservationData['reservation']['user'];
            $user = $entityManager->getRepository(StandardUser::class)->find($userId);

            if (!$user) {
                $this->addFlash('error', 'Utilisateur introuvable, veuillez vous reconnecter ou créer un compte.');
                return $this->redirectToRoute('app_register');
            }
        }

        if (!$request->query->has('reservation')) {
            $this->addFlash('error', 'Réservation incomplète, veuillez recommencer.');
            $updatedQuery = http_build_query([
                'reservation' => $reservationData,
                'user' => $user->getId()
            ]);
            return $this->redirectToRoute('make_reservation', ['reservation' => $updatedQuery]);
        }

        $company = $entityManager->getRepository(SportCompany::class)->find($reservationData['reservation']['company']);
        $service = $entityManager->getRepository(Service::class)->find($reservationData['reservation']['service']);
        $terrain = $entityManager->getRepository(Terrain::class)->find($reservationData['reservation']['terrain']);

        if (!$company || !$service || !$terrain) {
            $this->addFlash('error', 'Réservation incorrecte ou déjà prise, veuillez recommencer.');
            return $this->redirectToRoute('make_reservation', [
                'reservation' => http_build_query($reservationData)
            ]);
        }

        $reservation = new Reservation();
        $reservation
            ->setStandardUser($user)
            ->setSportCompany($company)
            ->setService($service)
            ->setTerrain($terrain)
            ->setDate(new \DateTime($reservationData['reservation']['date']))
            ->setTime(new \DateTime($reservationData['reservation']['time']));

        $form = $this->createForm(ReservationType::class, $reservation, ['company' => $company]);

        $validationResult = $validator->validate($reservation);
        if (!$validationResult->isValid()) {
            foreach ($validationResult->getErrors() as $error) {
                $form->addError(new FormError($error));
            }
            return $this->redirectToRoute('make_reservation');
        }

        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation créée avec succès !');
        if(isset($reservationData['user']) && $reservationData['user']){
            $this->addFlash('success', 'Veuillez vous connecter pour voir votre réservation.');
        }
        return $this->redirectToRoute('reservation_details', ['id' => $reservation->getId()]);
    }

    private function getOpeningHours(SportCompany $company): array
    {
        $openingHours = [];
        $dayTranslations = [
            'lundi' => 'monday',
            'mardi' => 'tuesday',
            'mercredi' => 'wednesday',
            'jeudi' => 'thursday',
            'vendredi' => 'friday',
            'samedi' => 'saturday',
            'dimanche' => 'sunday'
        ];

        foreach ($company->getSchedules() as $schedule) {
            $frenchDay = mb_strtolower($schedule->getDayOfWeek());
            $englishDay = $dayTranslations[$frenchDay] ?? $frenchDay;

            $openingHours[] = [
                'day' => $englishDay,
                'open' => $schedule->getOpeningTime()->format('H:i'),
                'close' => $schedule->getClosingTime()->format('H:i'),
            ];
        }

        return $openingHours;
    }

    #[Route('/reservation/{id}', name: 'reservation_details')]
    #[IsGranted('ROLE_USER', subject: 'reservation')]
    public function details(Reservation $reservation): Response
    {

        return $this->render('reservation/details.html.twig', [
            'reservation' => $reservation,
            'openingHours' => $reservation->getSportCompany()->getSchedules(),
        ]);
    }
}