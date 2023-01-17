<?php

namespace App\Controller;

use App\Entity\Pictures;
use App\Entity\Tricks;

use App\Form\TricksType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
     * 
     * @return void
     */
    #[Route('/tricks/add', name: 'app_user_tricks_add')]
    public function addTricks(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $tricks =  new Tricks();
        $formAddTrick = $this->createForm(TricksType::class, $tricks);
        $formAddTrick->handleRequest($request);

        if ($formAddTrick->isSubmitted() && $formAddTrick->isValid()) {
            $pictureFile =  $formAddTrick->get('picture')->getData();

            if ($pictureFile) {
                $originalFilename = $pictureFile;

                // may have multiple additionnals pictures
                $additionnalPictures = $formAddTrick->get('pictures')->getData();
                foreach ($additionnalPictures as $additionnalPicture) {
                    $file  = md5(uniqid()) . '.' . $additionnalPicture->guessExtension();
                    $additionnalPicture->move(
                        $this->getParameter('pictues_directory'),
                        $file
                    );
                    // in db 
                    $img = new Pictures();
                    $img->setPicure($file);
                    $tricks->addAdditionnalTrick($img);
                }

                $safeFilename = $slugger->slug($originalFilename);  // not used
                $newFilename = md5(uniqid()) . '.' . $originalFilename->guessExtension();
                $tricks->setPicture($newFilename);
                try {
                    $pictureFile->move(
                        $this->getParameter('pictues_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    //soit redirigé sur la page du formulaire en cas d'erreur, en précisant le(s) type(s) d'erreurs ;

                    $message = $this->addFlash('error', 'error type:' . $e); // or in twig

                    return $this->render('tricks/add.html.twig', [
                        'formAddTrick' =>  $formAddTrick->createView(),
                        'message' => $message
                    ]);
                }
                $tricks->setPicture($newFilename);
            }

            //    $txt = $formAddTrick->get('content')->getData();  to see later     

            $tricks->setUser($this->getUser());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($tricks);
            $entityManager->flush();

            $this->addFlash('success', 'Your trick have been added.');

            return $this->redirectToRoute('app_home');
        } //end form

        return $this->render('tricks/add.html.twig', [
            'formAddTrick' =>  $formAddTrick->createView(),
        ]);
    }
}
