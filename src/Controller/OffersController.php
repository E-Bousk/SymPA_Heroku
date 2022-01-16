<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Annonces;
use App\Entity\Comments;
use App\Form\OffersType;
use App\Form\SearchOfferType;
use App\Form\AnnonceContactType;
use App\Form\CommentsType;
use App\Repository\AnnoncesRepository;
use App\Services\ManagePicturesService;
use App\Services\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/offers", name="offers_")
 */
class OffersController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function index(AnnoncesRepository $annoncesRepository, Request $request): Response
    {
        // $offers = $annoncesRepository->fourchetteDate('2021-10-10', '2022-01-08', 3);
        $offers = $annoncesRepository->findBy(['active' =>true], ['created_at' => 'desc']);

        /** RECHERCHE par mot-clé ou catégorie */
        $form = $this->createForm(SearchOfferType::class);
        $search = $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $offers = $annoncesRepository->search(
                $search->get('mots')->getData(),
                $search->get('categorie')->getData()
            );
        }
        
        return $this->render('offers/index.html.twig', [
            'offers' => $offers,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"}, priority=-1)
     */
    public function show(Annonces $annonce): Response
    {
        return $this->render('offers/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }


    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, ManagePicturesService $managePicturesService): Response
    {
        $annonce = new Annonces();
        $form = $this->createForm(OffersType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setActive(false);
            $annonce->setUsers($this->getUser());

            // Récupère les images transmises
            $images = $form->get('images')->getData();

            // // Boucle sur les images
            // foreach($images as $image) {
            //     // Utilise la méthode créée (plus bas) pour générer un nom,
            //     // copier le fichier dans répertoire "upload/images/annonces"
            //     // et sauvegarder ce nom dans la BDD
            //     $img = $this->saveImage($image);
            //     $annonce->addImage($img);
            // }
            // Remplacé par 'managePicturesService':
            $managePicturesService->addImage($images, $annonce);

            // NOTE: On ajoute « cascade={"persist"} » sur la propriété « images » dans l'entité « annonces »
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offers/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/details/{slug}", name="details")
     */
    public function details(
        $slug,
        AnnoncesRepository $annoncesRepository,
        Request $request,
        SendMailService $sendMailService,
        EntityManagerInterface $em
        ): Response
    {
        $offer = $annoncesRepository->findOneBy(['slug' => $slug]);

        if (!$offer) {
            throw new NotFoundHttpException(sprintf('l\'annonce « %s » n\'a pas été trouvée', $slug));
        }

        $form = $this->createForm(AnnonceContactType::class);

        $contact = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sendMailService->send(
                $contact->get('email')->getData(),
                $offer->getUsers()->getEmail(),
                sprintf('Contact au sujet de votre annonce « %s ».', $offer->getTitle()),
                'contact_offers',
                [
                    'offer' => $offer,
                    // NOTE : ‼ « email » est un nom réservé ‼
                    'mail' => $contact->get('email')->getData(),
                    'message' => $contact->get('message')->getData()
                ]
            );

            $this->addFlash('message', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('offers_details', ['slug' => $offer->getSlug()]);
        }

        /** PARTIE COMMENTAIRES */
        $comment = new Comments;
        $commentForm = $this->createForm(CommentsType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(New \DateTime());
            $comment->setAnnonces($offer);

            // Récupère le contenu du champ « ParentId »
            $parentId = $commentForm->get('parentId')->getData();
            // Retrouve le commentaire correspondant
            if ($parentId != null) {
                $parent = $em->getRepository(Comments::class)->find($parentId);
            }
            $comment->setParent($parent ?? null);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('message', 'Votre commentaire a bien été envoyé !');
            return $this->redirectToRoute('offers_details', ['slug' => $slug]);
        }

        return $this->render('offers/details.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
            'commentForm' => $commentForm->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     */
    public function edit(
        Request $request,
        Annonces $annonce,
        EntityManagerInterface $entityManager,
        ManagePicturesService $managePicturesService
        ): Response
    {
        $form = $this->createForm(OffersType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            // foreach ($images as $image) {
            //     $img = $this->saveImage($image);
            //     $annonce->addImage($img);
            // }
            // Remplacé par 'managePicturesService' :
            $managePicturesService->addImage($images, $annonce);

            $entityManager->flush();

            return $this->redirectToRoute('users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offers/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Annonces $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offers_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/bookmark/add/{id}", name="add_bookmark")
     */
    public function addBookmark(Annonces $annonce): Response
    {
        if (!$annonce) {
            throw new NotFoundHttpException('Aucune annonce trouvée');
        }

        $annonce->addFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/bookmark/remove/{id}", name="remove_bookmark")
     */
    public function removeBookmark(Annonces $annonce): Response
    {
        if (!$annonce) {
            throw new NotFoundHttpException('Aucune annonce trouvée');
        }

        $annonce->removeFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/delete/image/{id}", name="delete_image", methods={"DELETE"})
     */
    public function deleteImage(Images $image, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // Vérifie que le CSRF token soit valide
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])) {
            // Supprime le fichier du répertoire 'upload/images/offers'
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

     // Méthode remplacée par 'managePicturesService':
    // /**
    //  * Lors de l'ajout d'image pour une annonce :
    //  * - Génère un nom unique aléatoire
    //  * - Déplace le fichier dans le répèrtoire 'upload/images/offers'
    //  * - Enregistre le nom dans la BDD (entité 'Images')
    //  * 
    //  * @param mixed $image 
    //  * @return Images 
    //  */
    // private function saveImage($image)
    // {
    //     // Génère un nouveau nom (unique) de fichier
    //     $fichier = md5(uniqid()) . '.' . $image->guessExtension();

    //     // Copie ce fichier dans le dossier 'upload/images/offers'
    //     $image->move(
    //         $this->getParameter('images_directory'),
    //         $fichier
    //     );

    //     // Stock le nom de l'image dans la BDD
    //     $img = new Images;
    //     $img->setName($fichier);

    //     // Retourne l'objet
    //     return $img;
    // }
}
