<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Images;
use App\Form\OffersType;
use App\Repository\AnnoncesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/offers")
 */
class OffersController extends AbstractController
{
    /**
     * @Route("/", name="offers_index", methods={"GET"})
     */
    public function index(AnnoncesRepository $annoncesRepository): Response
    {
        return $this->render('offers/index.html.twig', [
            'annonces' => $annoncesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="offers_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonces();
        $form = $this->createForm(OffersType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setActive(false);
            $annonce->setUsers($this->getUser());

            // Récupère les images transmises
            $images = $form->get('images')->getData();

            // Boucle sur les images
            foreach($images as $image) {
                // Utilise la méthode créée pour générer un nom,
                // copier le fichier dans répertoire "upload/images/annonces"
                // et sauvegarder ce nom dans la BDD
                $img = $this->saveImage($image);
                $annonce->addImage($img);
            }

            // NOTE: On ajoute « cascade={"persist"} » sur la propriété « images » dans l'entité « annonces »
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('offers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offers/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="offers_show", methods={"GET"})
     */
    public function show(Annonces $annonce): Response
    {
        return $this->render('offers/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="offers_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Annonces $annonce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffersType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $img = $this->saveImage($image);
                $annonce->addImage($img);
            }

            $entityManager->flush();

            return $this->redirectToRoute('offers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offers/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="offers_delete", methods={"POST"})
     */
    public function delete(Request $request, Annonces $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offers_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete/image/{id}", name="offers_delete_image", methods={"DELETE"})
     */
    public function deleteImage(Images $image, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // Vérifie que le CSRF token soit valide
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])) {
            // Supprime le fichier du HDD
            unlink($this->getParameter('images_directory').'/'.$image->getName());

            // Supprime l'entrée de la BDD
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // Répond en JSON
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Invalid token'], 400);
        }
    }


    /**
     * Lors de l'ajout d'image pour une annonce :
     * - Génère un nom unique aléatoire
     * - Déplace le fichier dans le répèrtoire 'upload/images/annonce'
     * - Enregistre le nom dans la BDD (entité 'Images')
     * 
     * @param mixed $image 
     * @return Images 
     */
    private function saveImage($image)
    {
        // Génère un nouveau nom (unique) de fichier
        $fichier = md5(uniqid()) . '.' . $image->guessExtension();

        // Copie ce fichier dans le dossier 'upload/images'
        $image->move(
            $this->getParameter('images_directory'),
            $fichier
        );

        // Stock le nom de l'image dans la BDD
        $img = new Images;
        $img->setName($fichier);

        // Retourne l'objet
        return $img;
    }
}
