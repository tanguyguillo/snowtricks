<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UserRepository;

namespace App\Controller;

use App\Entity\Tricks;
use  App\Repository\TricksRepository;
use  App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;;


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
     * function delete trick
     *
     */
    #[Route('/delete-tricks/{id}', name: 'app_tricks_delete', methods: ['POST'])]
    public function delete(Request $request, Tricks $trick, TricksRepository $tricksRepository): Response
    {
        /// we get data from json
        //$data = json_decode($request->getContent(), true);

        //dd($_POST);
        //         array:1 [▼
        //   "token" => "aaaad018109ff88b0e7c0920fa2f.G8yWKhdFstIFBQ2IKBLcVX-wDyVR6yqPv2axbxg8oJw.TLz7eGMWx4tTXX_tRniOMzbpO2ApuXL9iindWntS2bFapN1obXL3m090OQ"


        // we check the token
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            // if ($this->isCsrfTokenValid('delete' . $trick->getId(), $data['_token'])) {

            dd('bien');

            //$tricksRepository->remove($trick, true);

            // return new JsonResponse(['succes' => 1]);
        } else {
            //return new JsonResponse(['error' => 'Token Invalide'], 400);
            dd('mal');
        }
    }
}
