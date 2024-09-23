<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Establishment;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation/new/{id}', name: 'reservation_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, Establishment $establishment): Response
    {
        $reservation = new Reservation();
        $reservation->setEstablishment($establishment);

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('reservation_success');
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
            'establishment' => $establishment,
        ]);
    }
}