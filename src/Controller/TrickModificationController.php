<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class  TrickModificaionController
 */
#[Route('/tricks', name: 'tricks_')]
class TrickModificationController extends AbstractController
{

    /**
     * function modification
     */
    #[Route('/modification/{slug}', name: 'modification')]
    public function modification($slug, TricksRepository $tricksRepository, UserRepository $userRepository): Response
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);

        $AuthorId=$trick->getUser();
        $Author = $userRepository->findOneBy(['id' => $AuthorId]);
        // $userName = $user.username;
        // dd( $user.$userName);


        if(! $trick){
            throw new NotFoundHttpException("No trick found");
        }
        return $this->render('tricks/modification.html.twig', compact('trick', 'Author'));
    }
}