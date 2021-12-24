<?php

namespace App\Controller\Admin;

use App\Repository\AnnoncesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * @Route("/admin", name="admin_")
 * @package App\Controller\Admin
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/stats", name="stats")
     */
    public function statistics(CategoriesRepository $categoriesRepository, AnnoncesRepository $annoncesRepository)
    {
        // Récupère toutes les catégories
        $categories = $categoriesRepository->findAll();

        // Sépare les données tel qu'attendu par ChartJS
        $categoryName = [];
        $categoryColor = [];
        $categoryCount = [];

        foreach($categories as $categorie) {
            $categoryName[] = $categorie->getName();
            $categoryColor[] = $categorie->getColor();
            $categoryCount[] = count($categorie->getAnnonces());
        }

        // Cherche le nombre d'annonces par date
        $offers = $annoncesRepository->countByDate();

        // Sépare les données tel qu'attendu par ChartJS
        $offersDate = [];
        $offersCount = [];

        foreach($offers as $offer) {
            $offersDate[] = $offer['dateAnnonces'];
            $offersCount[] = $offer['count'];
        }
        
        // Crée la vue et envoie les données
        return $this->render('admin/stats.html.twig', [
            'categoryName' => json_encode($categoryName),
            'categoryColor' => json_encode($categoryColor),
            'categoryCount' => json_encode($categoryCount),
            'offersDate' => json_encode($offersDate),
            'offersCount' => json_encode($offersCount)
        ]);
    }


}
