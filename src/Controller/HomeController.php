<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class MainControler : homepage
 */
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TricksRepository $TricksRepository): Response
    {
        return $this->render('main_controler/index.html.twig', [
            'controller_name' => 'HomeController',
            'tricks' => $TricksRepository->findBy(['active' => true], ['created_at' => 'asc'])
        ]);
    }
}
