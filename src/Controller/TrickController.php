<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UserRepository;
use App\Controller\Tricks;

use App\Form\TricksType;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;




/**
 * class TrickController
 * 
 */
#[Route('/tricks', name: 'tricks_')]
class TrickController extends AbstractController
{
    /**
     * function details
     */
    #[Route('/details/{slug}', name: 'details')]
    #[Route('/details/modifications/{slug}', name: 'modifications')]
    public function details($slug, TricksRepository $tricksRepository, UserRepository $userRepository): Response
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);

        $AuthorId=$trick->getUser();
        $Author = $userRepository->findOneBy(['id' => $AuthorId]);

        if(! $trick){
            throw new NotFoundHttpException("No trick found");
        }
        return $this->render('tricks/details.html.twig', compact('trick', 'Author'));
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

        if ($formAddTrick->isSubmitted() && $formAddTrick->isValid()) {
            $pictureFile =  $formAddTrick->get('picture')->getData();

            if ($pictureFile) {
                $originalFilename = $pictureFile;
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


    /**
     * function modifications
     * tricks/details/modifications/stalefish
     */
    #[Route('edit/{slug}', name: 'edit')]
  public function edit($slug, TricksRepository $tricksRepository, UserRepository $userRepository): Response
   {
       $trick = $tricksRepository->findOneBy(['slug' => $slug]);

       $AuthorId=$trick->getUser();
       $Author = $userRepository->findOneBy(['id' => $AuthorId]);

      if(! $trick){
            throw new NotFoundHttpException("No trick found");
    }

    //     // we go to details but it's the modifications page also  : route modification
     return $this->render('tricks/details.html.twig', compact('trick', 'Author'));
   }

    

    /**
     * [Route('/delete-tricks/{slug}', name: 'tricks_delete'), methods={"DELETE"}]
     *
     */
    // public function deleteTricks(Tricks $triks, Request $request){


    
    // }
    

}


