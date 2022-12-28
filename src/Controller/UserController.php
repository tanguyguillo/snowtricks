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
use Symfony\Component\String\Slugger\SluggerInterface;


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
     *  https://symfony.com/doc/current/controller/upload_file.html
     * 
     * @return void
     */
    #[Route('/tricks/add', name: 'app_user_tricks_add')]
    public function addTricks(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $tricks =  new Tricks();
        $formAddTrick = $this->createForm(TricksType::class, $tricks);
        $formAddTrick->handleRequest($request);

        //dd($this->getParameter('pictues_directory'));

        if ($formAddTrick->isSubmitted() && $formAddTrick->isValid()) {
            $pictureFile =  $formAddTrick->get('picture')->getData();
            if ($pictureFile) {
                $originalFilename = $pictureFile;
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = md5(uniqid()) . '.' . $originalFilename->guessExtension();
                $tricks->setPicture($newFilename);

                try {
                    $pictureFile->move(
                        $this->getParameter('pictues_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $tricks->setPicture($newFilename);
            }
            // 'user_id' cannot be null
            $tricks->setUser($this->getUser());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($tricks);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('tricks/add.html.twig', [
            'formAddTrick' =>  $formAddTrick->createView(),
        ]);
    }
}
