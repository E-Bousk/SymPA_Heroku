<?php

namespace App\Controller;

use App\Form\EditProfileType;
use Dompdf\Dompdf;
use Dompdf\Options;
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

    /**
     * @Route("/data", name="users_data")
     */
    public function userData(): Response
    {
        return $this->render('users/data.html.twig');
    }

    /**
     * @Route("/data/download", name="users_data_download")
     */
    public function userDataDownload(): Response
    {
        // Définit les options du PDF
        $pdfOptions = new Options();

        // Police par défaut
        $pdfOptions->set('defaultFont', 'Arial');

        // Donne la possibilité de télécharger le PDF
        $pdfOptions->set('isRemoteEnabled', true);

        // Instancie DOMPDF
        $dompdf = new Dompdf($pdfOptions);

        // Pour gérer le SSL
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        $dompdf->setHttpContext($context);

        // Génère le HTML
        $html = $this->renderView('users/download.html.twig');
        // Transmet le HTML généré à DOMPDF
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();

        // Génère un nom de fichier
        $fileName = sprintf('user-data-%s-%s.pdf', $this->getUser()->getFirstName(), $this->getUser()->getName());

        // Output the generated PDF to Browser
        $dompdf->stream($fileName, [
            'Attachment' => true
        ]);

        // nécessaire car le "stream' n'est pas une réponse en tant que telle
        return new Response();
    }
}
