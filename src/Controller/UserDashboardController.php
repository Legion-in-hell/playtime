<?php

namespace App\Controller;

use App\Entity\StandardUser;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class UserDashboardController extends AbstractController
{
    #[Route('/account', name: 'user_dashboard')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        /** @var StandardUser $user */
        $user = $this->getUser();
        
        $reservations = $reservationRepository->findBy(['standardUser' => $user], ['dateTime' => 'DESC']);

        return $this->render('dashboard/user_dashboard.html.twig', [
            'user' => $user,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/account/edit', name: 'user_edit_profile')]

    public function editProfile(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/user_edit_profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/update', name: 'user_update_profile')]
    public function updateProfile(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/user_update_profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/reservations', name: 'user_reservations')]
    public function reservations(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/user_reservations.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/reservations/{id}', name: 'user_reservation')]
    public function reservation(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/user_reservation.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/reservations/{id}/cancel', name: 'user_cancel_reservation')]
    public function cancelReservation(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/user_cancel_reservation.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/reservations/{id}/edit', name: 'user_edit_reservation')]
    public function editReservation(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/user_edit_reservation.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/reservations/{id}/update', name: 'user_update_reservation')]
    public function updateReservation(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/user_update_reservation.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/reservations/{id}/delete', name: 'user_delete_reservation')]
    public function deleteReservation(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/user_delete_reservation.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/reservations/{id}/confirm', name: 'user_confirm_reservation')]
    public function confirmReservation(): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/user_confirm_reservation.html.twig', [
            'user' => $user,
        ]);
    }
}