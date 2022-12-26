<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class TrickController
 * /tricks//details/{slug}
 */
#[Route('/tricks', name: 'tricks_')]
class TrickController extends AbstractController
{
    #[Route('/details/{slug}', name: 'details')]
    public function details($slug, TricksRepository $tricksRepository): Response
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);

        if(! $trick){
            throw new NotFoundHttpException("No trick found");
        }
        return $this->render('tricks/details.html.twig', compact('trick'));
    }
}