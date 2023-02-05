<?php

namespace App\Controller;

use App\Entity\Tricks;

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
use App\Form\TricksType;
use App\Form\UpdateType;
use App\Form\CommentsType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Pictures;
use App\Entity\Comments;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * class TrickController
 * the create method is in UseController : addTricks
 * 
 */
#[Route('/tricks', name: 'tricks_')]
class TrickController extends AbstractController
{
    public $picturesRepository;
    public $tricksRepository;
    // private  $pictures;
    private $em;

    /**
     *  function __construct 
     *
     * @param PicturesRepository $picturesRepository, TricksRepository $tricksRepository
     */
    public function __construct(PicturesRepository $picturesRepository, TricksRepository $tricksRepository, EntityManagerInterface $em)
    {
        $this->picturesRepository = $picturesRepository;
        $this->tricksRepository = $tricksRepository;
        $this->em = $em;

        // $pictures = $this->pictures;
    }

    /**
     * function details (read)
     */
    #[Route('/details/{slug}', name: 'details')]
    public function details(EntityManagerInterface $entityManager, Request $request, $slug, TricksRepository $tricksRepository, UserRepository $userRepository, Tricks $tricks, PicturesRepository $picturesRepository): Response
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);
        if (!$trick) {
            throw new NotFoundHttpException("No trick found");
        }
        $AuthorId = $trick->getUser();
        $Author = $userRepository->findOneBy(['id' => $AuthorId]);
        $trickId = $trick->getId();
        $additionalPictures = $picturesRepository->findBy(['tricks' => $trickId]);
        $Image = $tricks->getPicture();
        $date = date('Y-m-d H:i:s');
        return $this->render('tricks/details.html.twig', compact('trick', 'Author', 'additionalPictures', 'Image', 'date'));
    }

    /**
     * update
     */
    #[Route('/details/modifications/{slug}', name: 'modifications')]
    public function Update(EntityManagerInterface $entityManager, Request $request, $slug, TricksRepository $tricksRepository, UserRepository $userRepository, Tricks $tricks, PicturesRepository $picturesRepository): Response
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);
        if (!$trick) {
            throw new NotFoundHttpException("No trick found");
        }
        $AuthorId = $trick->getUser();
        $Author = $userRepository->findOneBy(['id' => $AuthorId]);
        $trickId = $trick->getId();
        $additionalPictures = $picturesRepository->findBy(['tricks' => $trickId]);
        $Image = $tricks->getPicture();
        $formUpdateTrick = $this->createForm(UpdateType::class, $trick);

        $formUpdateTrick->handleRequest($request);
        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('update' . $trick->getId(), $submittedToken)) {
            $trick->setContent($formUpdateTrick->get('content')->getData());
            $trick->setCategory($formUpdateTrick->get('category')->getData());
            $trick->setModifiedAt(new \DateTimeImmutable("now"));
            $formUpdateTrick->get('title')->getData();
            $pictureFile =  $formUpdateTrick->get('picture')->getData();
            // may have multiple more pictures
            $morePictures = $formUpdateTrick->get('pictures')->getData(); // array

            if ($morePictures != []) {
                $this->addAdditionalPicture($morePictures, $tricks);
            }

            if (!$pictureFile == null) {
                if ($pictureFile) {
                    $originalFilename = $pictureFile;
                    $newFilename = md5(uniqid()) . '.' . $originalFilename->guessExtension();
                    $tricks->setPicture($newFilename);
                }
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // be redirected to the form page in the event of an error, specifying the type(s) of error;
                    $message = $this->addFlash('error', 'error type:' . $e); // or in twig
                    return $this->render('tricks/add.html.twig', [
                        'formAddTrick' =>  $formUpdateTrick->createView(),
                        'message' => $message
                    ]);
                }
            }

            $this->em->persist($tricks);
            $this->em->flush();

            $this->addFlash('success', 'Your trick have been updated.');
            return $this->redirectToRoute('app_home');
        }

        // $comment = new Comments;
        // $commentForm = $this->createForm(CommentsType::class, $comment);
        // $commentForm->handleRequest($request);

        // dd($trick);

        return $this->render('tricks/update.html.twig', compact('trick', 'Author', 'additionalPictures', 'formUpdateTrick', 'Image'));
    }

    /**
     * function delete 
     * trick for homePage with Ajax/JsonResponse
     *
     */
    #[Route('/delete-tricks/{id}', name: 'app_tricks_delete', methods: ['DELETE'])]
    public function delete(Request $request, Tricks $trick, TricksRepository $tricksRepository)
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $submittedToken)) {
            $trickId = $trick->getId();
            // 1 delete additional picture from server
            $this->deleteAdditionalPicture($trickId);
            // get the physical path of the main picture
            $mainPictureWithPath = $this->getParameter('pictures_directory') . '/' . $trick->getPicture();
            // 2 - delete trick from Bd
            $tricksRepository->remove($trick, true); // OK
            // 3 - delete Main picture on server
            if ($this->deletePicture($mainPictureWithPath)) {
                return new JsonResponse("oui", 200);
            } else {
                return new JsonResponse("non : delete picture ", 500);
            }
        } else {
            return new JsonResponse("non ", 500);
        }
    }

    /**
     * function deleteFromDetail 
     * (button delete all trick from modification page)
     */
    #[Route('/delete-tricks_from_detail/{id}', name: 'app_tricks_delete_from_detail', methods: ['Post'])]
    public function deleteFromDetail(Request $request, Tricks $trick, TricksRepository $tricksRepository)
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $submittedToken)) {
            $trickId = $trick->getId();
            // 1 delete additional picture from server
            $this->deleteAdditionalPicture($trickId);
            $mainPictureWithPath = $this->getParameter('pictures_directory') . '/' . $trick->getPicture();
            // 2 - delete trick from Bd
            $tricksRepository->remove($trick, true);
            // 3 - delete Main picture on server
            if ($this->deletePicture($mainPictureWithPath)) {
                $this->addFlash('success', 'Your trick have been deleted.');
            } else {
                $this->addFlash('error', 'Something goes wrong.');
            }
            // go back home
            return $this->redirectToRoute('app_home');
        }
    }

    /**
     *  function to delete 
     * the  adding picture in trick on server
     *
     * @param [type]  string (path of picture to delete on server) 
     * 
     * @return bool
     */
    private function deletePicture($PictureWithPath)
    {
        if (file_exists($PictureWithPath)) {
            unlink($PictureWithPath);
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * function delete Additional Picture
     *
     * @param [type] $argument (trick id)
     * @return void
     */
    public function deleteAdditionalPicture($argument)
    {
        // get the id of the trick
        $trickId = $argument; // $trickId = $trick->getId();
        // if this trick exist
        if ($this->tricksRepository->find($trickId) != null) {
            // get additional pictures list
            $additionalPictures = [];
            $additionalPictures = $this->picturesRepository->findBy(['tricks' => $trickId]);
            // if there is additional picture
            if ($additionalPictures != []) {
                // may have multiple pictures
                foreach ($additionalPictures as $additionalPicture) {
                    $file = $additionalPicture->getPicture();
                    // get the physical path
                    $additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;
                    // delete trick from server
                    if (file_exists($additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file)) {
                        unlink($additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file);
                    }
                } // end for each
            } else {
                // not additionalPicture to drop
            }
        }
    }

    /**
     * function to delete one additional picture by picture id from update screen
     *
     * @param [type] $argument   $pictureId, Request $request, Pictures $pictures
     * 
     * 
     * @return void
     */
    #[Route('/delete-additional-picture/{pictureId}', name: 'app_additional_picture_delete', methods: ['DELETE'])]
    public function deleteOneAdditionalPicture($pictureId, Request $request, picturesRepository $picturesRepository, Pictures $pictures)
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $pictureId, $submittedToken)) {
            $additionalPicture = $this->picturesRepository->find(array('id' => $pictureId)); // find : return an object 
            $file =  $additionalPicture->getPicture();
            // 1 get the physical path
            $additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;
            // 2 delete picture from server
            if ($this->deletePicture($additionalPictureWithPath)) {
                // 3 - delete from db
                $picturesRepository->removeElement($pictureId, true);

                // unset($this->picturesRepository[$pictureId]);
                return new JsonResponse("oui : additionalPicture", 200);
                //$this->addFlash('success', 'Your additional picture have been deleted.');
            } else {
                return new JsonResponse("non bbbb", 500);
                // $this->addFlash('error', 'Something goes wrong.');
            }
        }
    }


    /**
     * function addTricks
     * 
     * @return void
     */
    #[Route('/add', name: 'app_user_tricks_add')]
    public function addTricks(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $tricks =  new Tricks();
        $formAddTrick = $this->createForm(TricksType::class, $tricks);
        $formAddTrick->handleRequest($request);

        if ($formAddTrick->isSubmitted() && $formAddTrick->isValid()) {

            $pictureFile =  $formAddTrick->get('picture')->getData();

            if ($pictureFile) {
                $originalFilename = $pictureFile;

                // may have multiple more pictures
                $additionalPictures = $formAddTrick->get('pictures')->getData();
                $this->addAdditionalPicture($additionalPictures, $tricks);

                $safeFilename = $slugger->slug($originalFilename);  // not used
                $newFilename = md5(uniqid()) . '.' . $originalFilename->guessExtension();
                $tricks->setPicture($newFilename);
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception if something happens during file upload
                    //be redirected to the form page in the event of an error, specifying the type(s) of error;
                    $message = $this->addFlash('error', 'error type:' . $e);
                    return $this->render('tricks/add.html.twig', [
                        'formAddTrick' =>  $formAddTrick->createView(),
                        'message' => $message
                    ]);
                }
                $tricks->setPicture($newFilename);
            }
            $tricks->setUser($this->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($tricks);
            $entityManager->flush();
            $this->addFlash('success', 'Your trick have been added.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('tricks/add.html.twig', [
            'formAddTrick' =>  $formAddTrick->createView(),
        ]);
    }

    /**
     * function to add additional picture
     *
     * @return void
     */
    private function addAdditionalPicture($additionalPictures, $tricks)
    {
        foreach ($additionalPictures as $additionalPicture) {
            $file  = md5(uniqid()) . '.' . $additionalPicture->guessExtension();
            $additionalPicture->move(
                $this->getParameter('pictures_directory'),
                $file
            );
            // in db 
            $img = new Pictures();
            $img->setPicture($file);
            $tricks->addAdditionalTrick($img);
        }
    }

    /**
     * function error
     * 
     * @return  Response
     */
    #[Route('/error', name: 'app_user_tricks_error')]
    public function error($message): Response
    {
        $messageError = $message;
        return $this->render('tricks/error.html.twig', [
            'messageError' =>  $messageError(),
        ]);
    }

    /**
     * just to test http://127.0.0.1:8000/tricks/test/92  for example/testing
     */
    #[Route('/test/{pictureId}', name: 'app_test')]
    public function test(int $pictureId, Request $request, PicturesRepository $picturesRepository)
    {
        // $submittedToken = $request->request->get('_token');
        // if ($this->isCsrfTokenValid('delete' . $pictureId, $submittedToken)) {
        // $additionalPicture = $this->picturesRepository->find(array('id' => $pictureId)); // find : return an object 

        $additionalPicture = $this->picturesRepository->findOneById($pictureId);
        $file  = $additionalPicture->getPicture();

        // 1 get the physical path
        $additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;

        // 2 delete picture from server // yes have been found and deleted
        if ($this->deletePicture($additionalPictureWithPath)) {

            // 3 - delete additional picture from BD from db
            $this->em->remove($additionalPicture); // working
            $this->em->flush();

            return new JsonResponse("oui : additionalPictureDeleted", 200);
            //$this->addFlash('success', 'Your additional picture have been deleted.');
        } else {
            return new JsonResponse("non ", 500);
            // $this->addFlash('error', 'Something goes wrong.');
        }
    }

    // public function deleteSinglePicture(Pictures  $pictureId)
    // {
    //     $pictureId->setFieldData(null);
    //     $this->em->remove($pictureId);
    //     // $this->em->flush();
    // }
}
