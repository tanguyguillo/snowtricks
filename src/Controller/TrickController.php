<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UserRepository;


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

        $AuthorId = $trick->getUser();
        $Author = $userRepository->findOneBy(['id' => $AuthorId]);

        if (!$trick) {
            throw new NotFoundHttpException("No trick found");
        }
        return $this->render('tricks/details.html.twig', compact('trick', 'Author'));
    }

    /**
     * function modifications
     * tricks/details/modifications/stalefish
     */
    // #[Route('/details/modifications/{slug}', name: 'modifications')]
    // public function modification($slug, TricksRepository $tricksRepository, UserRepository $userRepository): Response
    // {
    //     $trick = $tricksRepository->findOneBy(['slug' => $slug]);

    //     $AuthorId=$trick->getUser();
    //     $Author = $userRepository->findOneBy(['id' => $AuthorId]);

    //     if(! $trick){
    //         throw new NotFoundHttpException("No trick found");
    //     }

    //     // we go to details but it's the modifications page also  : route modification
    //     return $this->render('tricks/details.html.twig', compact('trick', 'Author'));
    // }



    /**
     * [Route('/delete-tricks/{slug}', name: 'tricks_delete'), methods={"DELETE"}]
     *
     */
    // public function deleteTricks(Tricks $triks, Request $request){



    // }


}
