<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Form\SearchOfferType;
use App\Repository\AnnoncesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(
        AnnoncesRepository $annoncesRepository,
        CategoriesRepository $categoriesRepository,
        Request $request): Response
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
        $categories = $categoriesRepository->findAll();

        return $this->render('main/index.html.twig', compact('limit', 'page', 'offers', 'total', 'categories'));
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);

        $contact = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new TemplatedEmail())
                ->from($contact->get('email')->getData())
                ->to('petites.annonces@gmail.com')
                ->subject('Contact depuis le site « Petites annonces »')
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'mail' => $contact->get('email')->getData(),
                    'sujet' => $contact->get('sujet')->getData(),
                    'message' => $contact->get('message')->getData()
                ])
            ;

            $mailer->send($email);

            $this->addFlash('message', 'Email de contact envoyé');

            return $this->redirectToRoute('contact');
        }

        return $this->render('main/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
