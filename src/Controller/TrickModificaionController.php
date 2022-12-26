<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class  TrickModificaionController
 */
#[Route('/tricks', name: 'tricks_')]
class TrickModificaionController extends AbstractController
{

    /**
     * function modification
     */
    #[Route('/modification/{slug}', name: 'modification')]
    public function modification($slug, TricksRepository $tricksRepository): Response
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);

        if(! $trick){
            throw new NotFoundHttpException("No trick found");
        }
        return $this->render('tricks/modification.html.twig', compact('trick'));
    }
}