<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\Pictures;

use App\Repository\UserRepository;
use App\Repository\TricksRepository;
use App\Repository\PicturesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * class TrickController
 * the create method is in UseController : addTricks
 * 
 */
#[Route('/tricks', name: 'tricks_')]
class TrickController extends AbstractController
{
    /**
     * function details (read) 
     */
    #[Route('/details/{slug}', name: 'details')]
    #[Route('/details/modifications/{slug}', name: 'modifications')]
    public function details($slug, TricksRepository $tricksRepository, UserRepository $userRepository, Tricks $tricks, PicturesRepository $picturesRepository): Response
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);

        if (!$trick) {
            throw new NotFoundHttpException("No trick found");
        }

        $AuthorId = $trick->getUser();
        $Author = $userRepository->findOneBy(['id' => $AuthorId]);

        $trickId = $trick->getId();
        $additionnalPictures = $picturesRepository->findBy(['tricks' => $trickId]);

        return $this->render('tricks/details.html.twig', compact('trick', 'Author', 'additionnalPictures'));
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


            // first delete additionnals pictures on the server
            // get the list of additional pictures for a trick
            // $additionnalPictures = $pictures->getPicure();
            // foreach ($additionnalPictures as $additionnalPicture) {

            //     // $file  = md5(uniqid()) . '.' . $additionnalPicture->guessExtension();
            //     // $additionnalPicture->move(
            //     //     $tricksRepository->remove($trick, true)
            //     // );
            // }


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

    // private function deleteMainPicture($mainPictureWithPath)
    // {
    //     if (file_exists($mainPictureWithPath)) {
    //         unlink($mainPictureWithPath);
    //         return 1;
    //     } else {
    //         return 0;
    //     }
    // }

    /**
     * function edit
     */
    #[Route('/{id}/edit', name: 'app_tricks_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tricks $tricks, TricksRepository $tricksRepository, SluggerInterface $slugger): Response
    {
        // $form = $this->createForm(TricksType::class, $trick);
        // $form->handleRequest($request);

        $formAddTrick = $this->createForm(TricksType::class, $tricks);
        $formAddTrick->handleRequest($request);

        if ($formAddTrick->isSubmitted() && $formAddTrick->isValid()) {

            $pictureFile =  $formAddTrick->get('picture')->getData();

            if ($pictureFile) {
                $originalFilename = $pictureFile;

                // may have multiple additionnals pictures
                $additionnalPictures = $formAddTrick->get('pictures')->getData();
                foreach ($additionnalPictures as $additionnalPicture) {
                    $file  = md5(uniqid()) . '.' . $additionnalPicture->guessExtension();
                    $additionnalPicture->move(
                        $this->getParameter('pictues_directory'),
                        $file
                    );
                    // in db 
                    $img = new Pictures();
                    $img->setPicure($file);
                    $tricks->addAdditionnalTrick($img);
                }

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

            $tricksRepository->save($tricks, true);

            // return $this->redirectToRoute('app_tricks_index', [], Response::HTTP_SEE_OTHER);
        }

        // this route is used for read update dans
        return $this->render('tricks/add.html.twig', [
            'formAddTrick' =>  $formAddTrick->createView(),
        ]);
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

        dd('test');
    }
}
