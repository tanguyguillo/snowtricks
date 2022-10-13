<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class  with home (/)
 */
class SnowTricksController extends AbstractController
{
    /**
     *  function home 
     *
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function home(): Response
    { 
        $title = "Snowtricks";

        return $this->render('snow_tricks/home.html.twig', [
            'controller_name' => 'SnowTricksController', [
                'title' => $title
            ]
        ]);
    }

    #[Route('/snow/tricks', name: 'app_snow_tricks')]
    public function index(): Response
    {
        return $this->render('snow_tricks/index.html.twig', [
            'controller_name' => 'SnowTricksController',
        ]);
    }

    /**
     * function createNewTrick : http://127.0.0.1:8000/index.php/create-a-new-trick
     *
     * @return Response
     */
    #[Route('/create-a-new-trick', name: 'createNewTrick')]
    public function createNewTrick(): Response
    {
        return $this->render('snow_tricks/createNewTrick.html.twig', [
            'controller_name' => 'SnowTricksController',
        ]);
    }

    /**
     * function modifyAtrick :  http://snow-tricks.local/index.php/modify-a-trick
     *
     * @return Response
     */
    #[Route('/modify-a-trick', name: 'modifyAtrick')]
    public function modifyAtrick(): Response
    {
        return $this->render('snow_tricks/modifyAtrick.html.twig', [
            'controller_name' => 'SnowTricksController',
        ]);
    }

    /**
     * function modifyAtricks   http://snow-tricks.local/index.php/presentation-of-a-trick
     *
     * @return Response
     */
    #[Route('/presentation-of-a-trick', name: 'presentationOfAtrick')]
    public function modifyAtricks(): Response
    {
        return $this->render('snow_tricks/presentationOfAtrick.html.twig', [
            'controller_name' => 'SnowTricksController',
        ]);
    }
}
