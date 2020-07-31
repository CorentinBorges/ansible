<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Image;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TricksController extends AbstractController
{

    /**
     * @Route("/", name="app_homepage")
     */
    public function index(FigureRepository $figureRepository,Request $request)
    {


        $tricks = $figureRepository->findAll();
        return $this->render('tricks/index.html.twig', [
            'controller_name' => 'TricksController',
            'tricks' => $tricks,

        ]);
    }

    /**
     * @Route("/trick/{id}", name="app_show")
     */
    public function show(Figure $figure)
    {
        //Todo: afficher une image par défaut si elle n'existe pas
        return $this->render("tricks/show.html.twig",[
            'trick'=> $figure,

        ]);
    }
}
