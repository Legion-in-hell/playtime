<?php
namespace App\Controller;

use App\Entity\Establishment;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        return $this->render('/pages/home.html.twig');
    }

    #[Route('/search', name: 'search_results')]
    public function search(Request $request): Response
    {
        $searchTerm = $request->query->get('search');
        $results = [];

        if ($searchTerm) {
            $results = $this->entityManager->getRepository(Establishment::class)->findBySearchTerm($searchTerm);
            $this->addFlash('info', "Résultats de recherche pour: " . $searchTerm);
        }

        return $this->render('/pages/home.html.twig', [
            'search_results' => $results,
        ]);
    }
}
