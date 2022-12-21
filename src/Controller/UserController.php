<?php

namespace App\Controller;

use App\Controller\Tricks;
use App\Entity\Tricks;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    #[Route('/tricks/details/add', name: 'app_user_tricks_add')]
    public function addTricks()
    {
        $tricks =  new Tricks;

        $form = $this->createForm(TricksType::class, $tricks );

    

        return $this->render('tricks/add.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }
}
