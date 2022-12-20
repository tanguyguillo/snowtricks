<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;





class MainControlerController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        $this->addFlash('success', 'Your email address have to be verified.');

        return $this->render('main_controler/index.html.twig', [
            'controller_name' => 'MainControlerController',
        ]);
    }
}
