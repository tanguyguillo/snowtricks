<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;






/**
 * class MainControler route /
 */
class MainControlerController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        // verified the user is verified
         $user = new User;

        // if ($user->getVerified() == 0 ){
        //     //$this->addFlash('success', 'Your email address have to be verified.');
        //     var_dump('verified : '.$user->getVerified);
        // }




        return $this->render('main_controler/index.html.twig', [
            'controller_name' => 'MainControlerController',
        ]);
    }
}
