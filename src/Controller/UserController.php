<?php

namespace App\Controller;


use App\Entity\Tricks;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\TricksType;

/**
 * UserController
 */
class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    // #[Route('/tricks/details/add', name: 'app_user_tricks_add')]
    #[Route('/tricks/details/add', name: 'app_user_tricks_add')]
    public function addTricks()
    {
        $tricks =  new Tricks;

        $form = $this->createForm(TricksType::class, $tricks ); 

        return $this->render('user/tricks/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
