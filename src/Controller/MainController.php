<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\AnnoncesRepository;
use App\Repository\CategoriesRepository;
use App\Services\SendMailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(
        AnnoncesRepository $annoncesRepository,
        CategoriesRepository $categoriesRepository,
        Request $request,
        CacheInterface $cache
        ): Response
    {
        /** PAGINATION */
        // Définit le nombre d'élément par page
        $limit = 5;

        // Récupère sur quelle page se trouve l'utilisateur
        $page = (int)$request->query->get("page", 1);

        // Récupère les filtres (pour filtrer par catégorie)
        $filter = $request->get('categories');
  
        // Récupère les annonces de la page en fonction du filtre
        $offers = $annoncesRepository->getPaginatedOffers($page, $limit, $filter);

        // Récupère le nombre total d'annonce
        $total = $annoncesRepository->getTotalOffers($filter);

        /** FILTRE par catégorie */
        // Vérifie si une requête Ajax est faite
        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('main/_content.html.twig', compact('limit', 'page', 'offers', 'total'))
            ]);
        }

        // Récupère toutes les catégories
        // $categories = $categoriesRepository->findAll();
        // Mise en cache :
        $categories = $cache->get('categories_list', function(ItemInterface $item) use ($categoriesRepository) {
            $item->expiresAfter(3600);
            return $categoriesRepository->findAll();
        });

        return $this->render('main/index.html.twig', compact('limit', 'page', 'offers', 'total', 'categories'));
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, SendMailService $sendMailService): Response
    {
        $form = $this->createForm(ContactType::class);

        $contact = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sendMailService->send(
                $contact->get('email')->getData(),
                'petites.annonces@gmail.com',
                'Contact depuis le site « Petites annonces »',
                'contact',
                [
                    'mail' => $contact->get('email')->getData(),
                    'sujet' => $contact->get('sujet')->getData(),
                    'message' => $contact->get('message')->getData()
                ]
            );

            $this->addFlash('message', 'Email de contact envoyé');

            return $this->redirectToRoute('contact');
        }

        return $this->render('main/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
