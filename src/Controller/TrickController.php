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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * function delete trick
     * #[Route('/delete-tricks/{slug}', name: 'app_tricks_delete', methods: ['POST'])]
     */
    // 
    #[Route('/delete-tricks/{id}', name: 'app_tricks_delete', methods: ['POST'])]
    public function delete(Request $request, Tricks $trick, TricksRepository $tricksRepository): Response
    {
        // if ($request->isXMLHttpRequest()) {
        //     var_dump("dfdfdfd");
        // }
        // if ($request->get('ajax')) {
        //     return "ok";
        // }


        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {

            $tricksRepository->remove($trick, true);

            return $this->redirectToRoute('tricks_', [], Response::HTTP_SEE_OTHER);
        } else {
        }

        return $this->redirectToRoute('tricks_', [], Response::HTTP_SEE_OTHER);
    }

    // /**
    //  * @Route("/update", name="back_translation_update", methods="GET|POST")
    //  */
    // public function getById(Request $request, TranslationMessageRepository $messageRepository): JsonResponse
    // {
    //     $id = $request->query->get('id');
    //     $message = $messageRepository->find($id);

    //     if (!$message) {
    //         return new NotFoundHttpException();
    //     }

    //     return $this->json([
    //         'success' => true,
    //         'data' => $message
    //     ]);
    // }


    /**
     * Undocumented function
     * https://www.youtube.com/watch?v=apWjiEuDS0k&t=2036s
     *
     * @return void
     */
    // #[Route('/delete/picture/{id}', name: 'app_tricks_delete_picture', methods: ['DELETE'])]
    // public function deletePicture (Pictures $picture, Request $Request){

    //     $data = json_decote ($Request->getContent(), true); 
    //     $picture

    // $em = $this->getDoctrine()->getmMnager();
    // $em->remove($picture);
    // $em->Flush();

    //responf en json
    //     return new JsonResponse(['succes' => 1]);
    // }else{}
    // return new JsonResponse(['error' => 'Token Invalid''], 400);
    //     // }
}
