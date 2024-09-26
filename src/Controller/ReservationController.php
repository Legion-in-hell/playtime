<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Schedule;
use App\Entity\SportCompany;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReservationController extends AbstractController
{
    #[Route('/reservation/new/{id}', name: 'reservation_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager, SportCompany $sportCompany): Response
    {
        $reservation = new Reservation();
        $reservation->setSportCompany($sportCompany);
        $reservation->setStandardUser($this->getUser());

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('reservation_success');
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
            'sportCompany' => $sportCompany,
        ]);
    }

    #[Route('/reservation/success', name: 'reservation_success')]
    public function success(): Response
    {
        return $this->render('reservation/success.html.twig');
    }
}