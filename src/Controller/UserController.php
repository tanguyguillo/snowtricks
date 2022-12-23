<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Form\TricksType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

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
     * https://symfony.com/doc/current/doctrine.html#persisting-objects-to-the-database
     * 
     * object(App\Entity\Tricks)#759 (9) { ["id":"App\Entity\Tricks":private]=> NULL ["title":"App\Entity\Tricks":private]=> NULL ["slug":"App\Entity\Tricks":private]=> NULL ["content":"App\Entity\Tricks":private]=> NULL ["created_at":"App\Entity\Tricks":private]=> object(DateTimeImmutable)#760 (3) { ["date"]=> string(26) "2022-12-23 09:37:12.442816" ["timezone_type"]=> int(3) ["timezone"]=> string(3) "UTC" } ["active":"App\Entity\Tricks":private]=> bool(true) ["user":"App\Entity\Tricks":private]=> NULL ["category":"App\Entity\Tricks":private]=> NULL ["description":"App\Entity\Tricks":private]=> NULL }
     *
     * @return void
     */
    #[Route('/tricks/details/add', name: 'app_user_tricks_add')]
    public function addTricks(ManagerRegistry $doctrine): Response
    {
        $tricks =  new Tricks;

        


        $formAddTrick = $this->createForm(TricksType::class, $tricks);


        if ($formAddTrick->isSubmitted() && $formAddTrick->isValid()) {
            $entityManager = $doctrine->getManager();

            $tricks->setSlug("test-slug");
            
            $entityManager->persist($tricks);
            $entityManager->flush();

            //return new Response('Saved new trick');
            // return $this->redirectToRoute('/');
        } else {
            //var_dump($tricks);
            // var_dump($formAddTrick);
            // exit;
            //return new Response('Not valid');
           //return $this->redirectToRoute('/');
        }


        return $this->render('user/tricks/add.html.twig', [
            'formAddTrick' =>  $formAddTrick->createView(),
        ]);
    }
}
