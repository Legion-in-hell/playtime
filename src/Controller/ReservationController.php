<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\SportCompany;
use App\Form\ReservationType;
use App\Service\ReservationValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation/new/{id}', name: 'make_reservation')]
    public function new(Request $request, EntityManagerInterface $entityManager, ReservationValidator $validator, SportCompany $company): Response
    {
        $reservation = new Reservation();
        $reservation->setSportCompany($company);
        
        $form = $this->createForm(ReservationType::class, $reservation, [
            'company' => $company,
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationResult = $validator->validate($reservation);
            if ($validationResult->isValid()) {
                $entityManager->persist($reservation);
                $entityManager->flush();

                $this->addFlash('success', 'Réservation créée avec succès.');
                return $this->redirectToRoute('reservation_success');
            } else {
                foreach ($validationResult->getErrors() as $error) {
                    $this->addFlash('error', $error);
                }
            }
        }

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

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
            'company' => $company,
            'openingHours' => json_encode($openingHours),
        ]);
    }

    #[Route('/reservation/{id}', name: 'reservation_details')]
    #[IsGranted('VIEW', subject: 'reservation')]
    public function details(Reservation $reservation): Response
    {
        return $this->render('reservation/details.html.twig', [
            'reservation' => $reservation,
            'openingHours' => $reservation->getSportCompany()->getSchedules(),
        ]);
    }
}