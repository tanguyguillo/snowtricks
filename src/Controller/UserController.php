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


/**
 * addTricks
 *
 * @return void
 */
    #[Route('/tricks/details/add', name: 'app_user_tricks_add')]
    public function addTricks()
    {
        $tricks =  new Tricks;

        $formAddTrick = $this->createForm(TricksType::class, $tricks ); 

        return $this->render('user/tricks/add.html.twig', [
            'formAddTrick' =>  $formAddTrick->createView(),
        ]);
    }
}
