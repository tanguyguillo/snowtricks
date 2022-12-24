<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * http://127.0.0.1:8000/tricks/details/stalefish
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

        // dd($trick);

        // return $this->render('main_controler/index.html.twig', [
        //     'controller_name' => 'MainControlerController',
        //     'tricks' => $TricksRepository->findBy(['active' => true], ['created_at' => 'asc'])
        // ]);

        return $this->render('tricks/details.html.twig', compact('trick'));
    }
}