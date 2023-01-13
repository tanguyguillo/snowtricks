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
    #[Route('/delete-tricks/{id}', name: 'app_tricks_delete', methods: ['DELETE'])]
    public function delete(Request $request, Tricks $trick, TricksRepository $tricksRepository)
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $submittedToken)) {

            //delete the trick in BD
            $tricksRepository->remove($trick, true); // OK
            //and delete pictures // 127 : 39a5ec187ad420e71980e27bca41ca46.png

            return new JsonResponse("oui", 200);
        } else {
            return new JsonResponse("non ", 500);
        }
    }
}
