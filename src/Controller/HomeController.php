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
        $searchTerm = $request->query->get('search');
        $results = [];

        if ($searchTerm) {
            $results = $this->sportCompanyRepository->findBySearchTerm($searchTerm);
            $this->addFlash('info', "Résultats de recherche pour: " . $searchTerm);
        }

        return $this->render('/pages/home.html.twig', [
            'search_results' => $results,
        ]);
    }

    #[Route('/company/{id}', name: 'company_details')]
    public function companyDetails(SportCompany $sportCompany): Response
    {
        return $this->render('/pages/companyDetails.html.twig', [
            'company' => $sportCompany,
        ]);
    }
}