<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use App\Repository\AnnoncesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/offers", name="admin_offers_")
 * @package App\Controller\Admin
 */
class OffersController extends AbstractController
{
	/**
	 * @Route("/", name="home")
	 */
	public function index(AnnoncesRepository $annoncesRepository): Response
	{
		return $this->render('admin/offers/index.html.twig', [
			'annonces' => $annoncesRepository->findAll()
		]);
	}

	/**
	 * @Route("/activate/{id}", name="activate")
	 */
	public function activate(Annonces $annonce): Response
	{
		$annonce->setActive(($annonce->getActive()) ? 0 : 1);

		$em = $this->getDoctrine()->getManager();
		$em->flush();

		return new Response("true");
	}

	/**
	 * @Route("/delete/{id}", name="delete")
	 */
	public function delete(Annonces $annonce): Response
	{
		$em = $this->getDoctrine()->getManager();
		$em->remove($annonce);
		$em->flush();

		$this->addFlash('message', 'Annonce supprimée avec succès');

		return $this->redirectToRoute('admin_offers_home');
	}
}
