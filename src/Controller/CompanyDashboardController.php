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
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createAccessDeniedException('User not found.');
        }

        $reservations = $this->entityManager->getRepository(Reservation::class)->findBy(['user' => $user]);

        return $this->render('dashboard/company_dashboard.html.twig', [
            'user' => $user,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/dashboard/establishment/update', name: 'establishment_update')]
    public function updateEstablishment(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('User not found.');
        }

        $form = $this->createForm(EstablishmentType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Les informations de votre établissement ont été mises à jour avec succès.');
            return $this->redirectToRoute('company_dashboard');
        }

        return $this->render('dashboard/establishment_update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/establishment/{id}/edit', name: 'establishment_edit')]
    #[IsGranted('ROLE_COMPANY')]
    public function editEstablishment(Request $request, Establishment $establishment): Response
    {
        if ($establishment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EstablishmentType::class, $establishment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('company_dashboard');
        }

        return $this->render('dashboard/establishment_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dashboard/establishment/{id}/delete', name: 'establishment_delete')]
    #[IsGranted('ROLE_COMPANY')]
    public function deleteEstablishment(Establishment $establishment): Response
    {
        if ($establishment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $this->entityManager->remove($establishment);
        $this->entityManager->flush();

        return $this->redirectToRoute('company_dashboard');
    }

    #[Route('/dashboard/establishment/{id}', name: 'establishment_show')]
    #[IsGranted('ROLE_COMPANY')]
    public function showEstablishment(Establishment $establishment): Response
    {
        if ($establishment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('dashboard/establishment_show.html.twig', [
            'establishment' => $establishment,
        ]);
    }

    #[Route('/dashboard/establishment/{id}/bookings', name: 'establishment_bookings')]
    #[IsGranted('ROLE_COMPANY')]
    public function showEstablishmentBookings(Establishment $establishment): Response
    {
        if ($establishment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('dashboard/establishment_bookings.html.twig', [
            'establishment' => $establishment,
        ]);
    }
}