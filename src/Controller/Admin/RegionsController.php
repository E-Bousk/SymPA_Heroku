<?php

namespace App\Controller\Admin;

use App\Entity\Regions;
use App\Form\RegionsType;
use App\Repository\RegionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/regions", name="admin_regions_")
 * @package App\Controller\Admin
 */
class RegionsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(RegionsRepository $regionsRepository): Response
    {
        return $this->render('admin/regions/index.html.twig', [
            'regions' => $regionsRepository->findAll()
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function ajoutRegions(Request $request): Response
    {
        $region = new Regions;

        $form = $this->createForm(RegionsType::class, $region);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($region);
            $em->flush();

            return $this->redirectToRoute('admin_regions_home');
        }

        return $this->render('admin/regions/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
