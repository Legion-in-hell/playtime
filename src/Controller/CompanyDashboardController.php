<?php

namespace App\Controller;

use App\Entity\Establishment;
use App\Form\EstablishmentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reservation;

class CompanyDashboardController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'company_dashboard')]
    public function index(): Response
    {
        $establishments = $this->entityManager->getRepository(Establishment::class)->findBy(['name' => $this->getUser()]);
        
        $reservations = [];
        foreach ($establishments as $establishment) {
            $establishmentReservations = $this->entityManager->getRepository(Reservation::class)->findBy(['establishment' => $establishment]);
            $reservations = array_merge($reservations, $establishmentReservations);
        }

        return $this->render('dashboard/company_dashboard.html.twig', [
            'establishments' => $establishments,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/dashboard/establishment/new', name: 'establishment_new')]
    public function newEstablishment(Request $request): Response
    {
        $establishment = new Establishment();
        $establishment->setCompany($this->getUser());
        $form = $this->createForm(EstablishmentType::class, $establishment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($establishment);
            $this->entityManager->flush();

            return $this->redirectToRoute('company_dashboard');
        }

        return $this->render('dashboard/establishment_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/dashboard/establishment/{id}/edit', name: 'establishment_edit')]
    // #[IsGranted('ROLE_COMPANY')]
    public function editEstablishment(Establishment $establishment): Response
    {
        if ($establishment->getCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EstablishmentType::class, $establishment);

        return $this->render('dashboard/establishment_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/establishment/{id}/delete', name: 'establishment_delete')]
    // #[IsGranted('ROLE_COMPANY')]
    public function deleteEstablishment(Establishment $establishment): Response
    {
        if ($establishment->getCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($establishment);
        $entityManager->flush();

        return $this->redirectToRoute('company_dashboard');
    }

    #[Route('/dashboard/establishment/{id}', name: 'establishment_show')]
    // #[IsGranted('ROLE_COMPANY')]
    public function showEstablishment(Establishment $establishment): Response
    {
        if ($establishment->getCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('dashboard/establishment_show.html.twig', [
            'establishment' => $establishment,
        ]);
    }

    #[Route('/dashboard/establishment/{id}/bookings', name: 'establishment_bookings')]
    // #[IsGranted('ROLE_COMPANY')]
    public function showEstablishmentBookings(Establishment $establishment): Response
    {
        if ($establishment->getCompany() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('dashboard/establishment_bookings.html.twig', [
            'establishment' => $establishment,
        ]);
    }

}