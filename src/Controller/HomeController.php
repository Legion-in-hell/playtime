<?php
namespace App\Controller;

use App\Entity\SportCompany;
use App\Repository\SportCompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    private $entityManager;
    private $sportCompanyRepository;
    
    public function __construct(EntityManagerInterface $entityManager, SportCompanyRepository $sportCompanyRepository)
    {
        $this->entityManager = $entityManager;
        $this->sportCompanyRepository = $sportCompanyRepository;
    }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $sportCompanies = $this->sportCompanyRepository->findAllWithImages();

        $companiesForMap = array_map(function($company) {
            return [
                'id' => $company->getId(),
                'name' => $company->getName(),
                'address' => $company->getAddress(),
                'latitude' => $company->getLatitude(),
                'longitude' => $company->getLongitude(),
            ];
        }, $sportCompanies);

        return $this->render('/pages/home.html.twig', [
            'sportCompanies' => $sportCompanies,
            'companiesForMap' => json_encode($companiesForMap),
        ]);
    }

    #[Route('/search', name: 'search_results')]
    public function search(Request $request): Response
    {
        $venue = $request->query->get('venue');
        $date = $request->query->get('date');

        if (!$venue || !$date) {
            $this->addFlash('error', 'Veuillez sélectionner un lieu et une date.');
            return $this->redirectToRoute('home');
        }

        $user = $this->getUser();

        if ($user) {
            // L'utilisateur est connecté, redirigez-le vers la page de réservation
            return $this->redirectToRoute('reservation_page', [
                'venue' => $venue,
                'date' => $date
            ]);
        } else {
            // L'utilisateur n'est pas connecté, redirigez-le vers la page d'inscription "Joueur"
            return $this->redirectToRoute('app_register', [
                'venue' => $venue,
                'date' => $date
            ]);
        }
    }
    
    #[Route('/company/{id}', name: 'company_details')]
    public function companyDetails(SportCompany $sportCompany): Response
    {
        return $this->render('/pages/companyDetails.html.twig', [
            'company' => $sportCompany,
        ]);
    }
}