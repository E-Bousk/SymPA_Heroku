<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/users")
 * @package App\Controller
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="users")
     */
    public function index(): Response
    {
        return $this->render('users/index.html.twig');
    }

    /**
     * @Route("/offers/create", name="users_offers_create")
     */
    public function ajoutAnnonce(Request $request): Response
    {
        $annonce = new Annonces;

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setActive(false);
            $annonce->setUsers($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/edit", name="users_edit_profile")
     */
    public function editProfil(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('users');
        }

        return $this->render('users/editprofile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/edit", name="users_edit_password")
     */
    public function editPass(Request $request, UserPasswordEncoderInterface $PasswordEncoder): Response
    {

        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();


            if ($PasswordEncoder->isPasswordValid($user, $request->request->get('currentPass'))) {
                if ($request->request->get('pass1') === $request->request->get('pass2')) {
                    $user->setPassword($PasswordEncoder->encodePassword($user, $request->request->get('pass1')));
                    $em->flush();

                    $this->addFlash('message', 'Mot de passe modifié avec succès !');
                    return $this->redirectToRoute('users');
                } else {
                    $this->addFlash('error', 'Les mots de passe ne sont pas identiques !');
                }
            } else {
                $this->addFlash('error', 'Mot de passe actuel incorrect !');
            }
        }
        return $this->render('users/editpassword.html.twig');
    }
}
