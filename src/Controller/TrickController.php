<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Repository\UserRepository;
use App\Repository\TricksRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use function PHPUnit\Framework\returnSelf;;

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
     * function delete trick for homePage in Ajax
     *
     */
    #[Route('/delete-tricks/{id}', name: 'app_tricks_delete', methods: ['DELETE'])]
    public function delete(Request $request, Tricks $trick, TricksRepository $tricksRepository)
    {
        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $submittedToken)) {
            // get the physical path
            $mainPictureWithPath = $this->getParameter('pictues_directory') . '/' . $trick->getPicture();
            // delete trick from Bd
            $tricksRepository->remove($trick, true); // OK
            // delete on server
            if ($this->deleteMainPicture($mainPictureWithPath)) {
                return new JsonResponse("oui", 200);
            } else {
                return new JsonResponse("non : delete picture ", 500);
            }
        } else {
            return new JsonResponse("non ", 500);
        }
    }

    /**
     *  function to delete the main picture in trick
     *
     * @param [type] $mainPictureWithPath string (path of picture to delete on server)
     * @param TricksRepository $tricksRepository
     * @return bool
     */
    private function deleteMainPicture($mainPictureWithPath)
    {
        if (file_exists($mainPictureWithPath)) {
            unlink($mainPictureWithPath);
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * function and route for testing
     * warning the route are "additionnal with the class"
     *
     */
    #[Route('/test/{argument}', name: 'app_tricks_test',)]
    public function test($argument, TricksRepository $tricksRepository): Response
    {
        //and  delete Mainpicture // 125 : e14c2c5acb6440d8a2aa89fdd187893d.png"  and after todo else pictures
        $id = $argument;
        $trick = $tricksRepository->findOneBy(['id' => $id]);
        // $mainPicture = $trick->getPicture();

        $mainPictureWithPath = $this->getParameter('pictues_directory') . '/' . $trick->getPicture();
        //check if ok
        if (file_exists($mainPictureWithPath)) {
            unlink($mainPictureWithPath);
            var_dump('ok');
        } else {
            var_dump('pas ok');
        }

        dd(ghghghgh);
    }
}
