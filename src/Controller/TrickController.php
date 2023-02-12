<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\Pictures;
use App\Entity\Comments;
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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\TricksType;
use App\Form\UpdateType;
use App\Form\PicturesType;
use App\Form\CommentsType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\FileBag;

/**
 * class TrickController
 *
 * 
 */
#[Route('/tricks', name: 'tricks_')]
class TrickController extends AbstractController
{
    public $picturesRepository;
    public $tricksRepository;
    // public $tricks;
    private $em;

    /**
     *  function __construct 
     *
     * @param PicturesRepository $picturesRepository, TricksRepository $tricksRepository, EntityManagerInterface $em, Tricks $tricks
     */
    public function __construct(PicturesRepository $picturesRepository, TricksRepository $tricksRepository, EntityManagerInterface $em)
    {
        $this->picturesRepository = $picturesRepository;
        $this->tricksRepository = $tricksRepository;
        // $this->tricks = $tricks;
        $this->em = $em;
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
     * Function update (write)
     */
    #[Route('/details/modifications/{slug}', name: 'modifications')]
    public function Update(EntityManagerInterface $entityManager, Request $request, $slug, TricksRepository $tricksRepository, UserRepository $userRepository,  Tricks $tricks, PicturesRepository $picturesRepository, Pictures $pictures): Response
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

        $formIndividualPicture = $this->createForm(picturesType::class, $pictures);

        if ($this->isCsrfTokenValid('update' . $trick->getId(), $submittedToken)) {
            $trick->setContent($formUpdateTrick->get('content')->getData());
            $trick->setCategory($formUpdateTrick->get('category')->getData());
            $trick->setModifiedAt(new \DateTimeImmutable("now"));
            // $formUpdateTrick->get('title')->getData();
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

            $trick->setModifiedAt(new \DateTimeImmutable("now"));
            $this->em->persist($tricks);
            $this->em->flush();

            $this->addFlash('success', 'Your trick have been updated.');
            return $this->redirectToRoute('app_home');
        }

        // $comment = new Comments;
        // $commentForm = $this->createForm(CommentsType::class, $comment);
        // $commentForm->handleRequest($request);

        // dd($trick);

        return $this->render('tricks/update.html.twig', compact('trick', 'Author', 'additionalPictures', 'formUpdateTrick', 'Image', 'formIndividualPicture'));
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
     * function delete Additional from Entity PicturesPicture
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
     * @param [type]  $pictureId
     * 
     * 
     *
     * @return void
     */
    #[Route('/delete-picture/{pictureId}', name: 'app_delete_picture', methods: ['DELETE'])]
    public function deleteOneAdditionalPicture(int $pictureId, Request $request)
    {
        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $pictureId, $submittedToken)) {

            $additionalPicture = $this->picturesRepository->find(array('id' => $pictureId)); // find : return an object used also in remove

            $file =  $additionalPicture->getPicture();
            // 1 get the physical path
            $additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;
            // 2 delete picture from server
            if ($this->deletePicture($additionalPictureWithPath)) {
                // 3 - delete additional picture from BD from db
                $this->em->remove($additionalPicture);
                $this->em->flush();
                return new JsonResponse("oui : additionalPictureDeleted", 200);
            } else {
                return new JsonResponse("non ", 500);
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
     * function deleteMainPictureOnly : delete only main picture on detail page
     *  http://127.0.0.1:8000/tricks/delete-main-picture-only/224   
     */
    #[Route('/delete-main-picture-only/{trickId}', name: 'app_delete_main_only', methods: ['DELETE'])]
    public function deleteMainPictureOnly(int $trickId, Request $request)
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $trickId, $submittedToken)) {
            $trick = $this->tricksRepository->findOneById($trickId);
            $file  = htmlentities($trick->getPicture());
            // 1 get the physical path
            $PictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;
            // 2 delete picture from server // yes have been found and deleted
            //$tricks->setPicture() = "empty.png";

            if ($this->deletePicture($PictureWithPath)) {

                // $this->setEmpty("empty.png", $trickId);
                // $entityManager = $doctrine->getManager();
                // $entityManager->persist($tricks);
                // $entityManager->flush();

                return new JsonResponse("oui : additionalPictureDeleted", 200);
            } else {
                return new JsonResponse("non ", 500);
            }
        }
    }

    /**
     *  function set picture empty for main picture
     *
     * @param string $fileName
     * @param [type] $trickId
     * @return void
     */
    private function setEmpty(string $fileName, $trickId)
    {
        $trick = $this->tricksRepository->findOneById($trickId);
        $trick->setPicture($fileName);
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
     * just to test http://127.0.0.1:8000/tricks/test/133 for example/testing
     */
    #[Route('/test/{pictureId}', name: 'app_delete_individual_picture')]
    public function test(int $pictureId, Request $request, PicturesRepository $picturesRepository)
    {
        $additionalPicture = $this->picturesRepository->findOneById($pictureId);
        $file  = $additionalPicture->getPicture();

        // 1 get the physical path
        $additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;

        // 2 delete picture from server // yes have been found and deleted
        if ($this->deletePicture($additionalPictureWithPath)) {

            // 3 - delete additional picture from BD from db
            $this->em->remove($additionalPicture);
            $this->em->flush();

            return new JsonResponse("oui : additionalPictureDeleted", 200);
        } else {
            return new JsonResponse("non ", 500);
            // $this->addFlash('error', 'Something goes wrong.');
        }
    }

    /**
     *  
     * Function update individual picture (write)
     */
    #[Route('/individual/{pictureId}', name: 'app_individual')]
    public function individual(int $pictureId, Request $request, TricksRepository $tricksRepository)
    {
        $submittedToken = $request->request->get('_token');

        dd('test');

        if ($this->isCsrfTokenValid('updateAdditionalPicture' . $pictureId, $submittedToken)) {

            // $additionalPicture = ($request->files);
            // $file  = md5(uniqid()) . '.' . $additionalPicture->guessExtension();

            // //$pictureFile =  $request->get('picture')->getData();

            // dd($additionalPicture);


            //1- import new picture
            //$file  = md5(uniqid()) . '.' . $additionalPicture->guessExtension();
            // $additionalPicture->move(
            //     $this->getParameter('pictures_directory'),
            //     $file
            // );

            // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //     $additionalPicture = ($_FILES['file']['name']);
            //     // $this->addAdditionalPicture($file, $tricks); ????

            //     // 1- import new picture
            //     // $file  = md5(uniqid()) . '.' . $additionalPicture->guessExtension();
            //     // $additionalPicture->move(
            //     //     $this->getParameter('pictures_directory'),
            //     //     $file
            //     // );
            // }
            dd($file);



            // $additionalPicture = $this->picturesRepository->findOneById($pictureId);
            // $file  = $additionalPicture->getPicture();

            // $additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;


            // if ($this->deletePicture($additionalPictureWithPath)) {

            //     $this->em->remove($additionalPicture);
            //     $this->em->flush();

            //     return new JsonResponse("oui : additionalPictureDeleted", 200);
            // } else {
            //     return new JsonResponse("non ", 500);


        }
    }


    /**
     * just to test http://127.0.0.1:8000/tricks/test2/223 for example/testing  , Tricks $tricks
     */
    #[Route('/test2/{trickId}', name: 'app_test2')]
    public function test2(int $trickId, Request $request, TricksRepository $tricksRepository)
    {
        $trick = $this->tricksRepository->findOneById($trickId);
        $file  = $trick->getPicture();
        // // 1 get the physical path
        // $PictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;
        // // 2 delete picture from server // yes have been found and deleted
        // //  $tricks->setPicture() = "empty.png";
        // // $tricks->setPicture('empty.png');
        // if ($this->deletePicture($PictureWithPath)) {
        //     return new JsonResponse("oui : additionalPictureDeleted", 200);
        // } else {
        //     return new JsonResponse("non ", 500);
        // }
    }




    /********************* functions shared ****************************/

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
}
