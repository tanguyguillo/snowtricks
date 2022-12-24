<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\User;

use App\Form\TricksType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;

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
     * function addTricks
     * 
     * 
     * @return void
     */
    #[Route('/tricks/details/add', name: 'app_user_tricks_add')]
    public function addTricks(Request $request, ManagerRegistry $doctrine): Response
    {
        $tricks =  new Tricks;

        // get the current user
        $tricks->setUser($this->getUser());

        $formAddTrick = $this->createForm(TricksType::class, $tricks);
        $formAddTrick->handleRequest($request);

        if ($formAddTrick->isSubmitted() && $formAddTrick->isValid()) {
            $entityManager = $doctrine->getManager();

            $tricks->setSlug("test-slug");

            $entityManager->persist($tricks);
            $entityManager->flush();

            //return $this->redirectToRoute('/');
        }

        else {
           // return new Response('Not valid');
        }

        return $this->render('user/tricks/add.html.twig', [
            'formAddTrick' =>  $formAddTrick->createView(),
        ]);
    }
}
