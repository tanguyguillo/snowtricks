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
        $additionnalPictures = $picturesRepository->findBy(['tricks' => $trickId]);
        $Image = $tricks->getPicture();
        $date = date('Y-m-d H:i:s');
        return $this->render('tricks/details.html.twig', compact('trick', 'Author', 'additionnalPictures', 'Image', 'date'));
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
        $additionnalPictures = $picturesRepository->findBy(['tricks' => $trickId]);
        $Image = $tricks->getPicture();

        $formUpdateTrick = $this->createForm(Updatetype::class, $trick);
        $formUpdateTrick->handleRequest($request);
        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('update' . $trick->getId(), $submittedToken)) {
            $trick->setContent($formUpdateTrick->get('content')->getData()); // OK
            $trick->setCategory($formUpdateTrick->get('category')->getData()); // OK 
            $trick->setModifiedAt(new \DateTimeImmutable("now")); // OK
            $formUpdateTrick->get('title')->getData();


            // dd($formUpdateTrick->get('title')->getData());
            //$trick->setTitle($formUpdateTrick->get('title')->getData()); // OK 
            // dd($formUpdateTrick->get('title')->getData());
            // $trick->setTitle($formUpdateTrick->get('title')->getData()); // OK

            $this->em->persist($tricks);
            $this->em->flush();

            $this->addFlash('success', 'Your trick have been updated.');
            return $this->redirectToRoute('app_home');
        }

        // $comment = new Comments;
        // $commentForm = $this->createForm(CommentsType::class, $comment);
        // $commentForm->handleRequest($request);

        // dd($trick);

        return $this->render('tricks/update.html.twig', compact('trick', 'Author', 'additionnalPictures', 'formUpdateTrick', 'Image'));
    }

    /**
     * function delete trick for homePage with Ajax/JsonResponse
     *
     */
    #[Route('/delete-tricks/{id}', name: 'app_tricks_delete', methods: ['DELETE'])]
    public function delete(Request $request, Tricks $trick, TricksRepository $tricksRepository)
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $submittedToken)) {
            $trickId = $trick->getId();
            // 1 delete additionnal picture from server
            $this->deleteAdditionnalPicture($trickId);
            // get the physical path of the main picture
            $mainPictureWithPath = $this->getParameter('pictues_directory') . '/' . $trick->getPicture();
            // 2 - delete trick from Bd
            $tricksRepository->remove($trick, true); // OK
            // 3 - delete Main picture on server
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
     * function deleteFromDetail (button delete from detail page)
     */
    #[Route('/delete-tricks_from_detail/{id}', name: 'app_tricks_delete_from_detail', methods: ['Post'])]
    public function deleteFromDetail(Request $request, Tricks $trick, TricksRepository $tricksRepository)
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $submittedToken)) {
            $trickId = $trick->getId();
            // 1 delete additionnal picture from server
            $this->deleteAdditionnalPicture($trickId);
            $mainPictureWithPath = $this->getParameter('pictues_directory') . '/' . $trick->getPicture();
            // 2 - delete trick from Bd
            $tricksRepository->remove($trick, true);
            // 3 - delete Main picture on server
            if ($this->deleteMainPicture($mainPictureWithPath)) {
                $this->addFlash('success', 'Your trick have been deleted.');
            } else {
                $this->addFlash('error', 'Something goes wrong.');
            }
            // go back home
            return $this->redirectToRoute('app_home');
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
     * function edit  tricks_app_tricks_edit
     */
    #[Route('/triks/edit/{id}', name: 'app_tricks_edit', methods: ['POST'])]
    public function edit(Request $request, Tricks $tricks, TricksRepository $tricksRepository, SluggerInterface $slugger): Response
    {

        dd('555555');

        $formAddTrick = $this->createForm(UpdateType::class, $tricks);
        $formAddTrick->handleRequest($request);

        if ($formAddTrick->isSubmitted() && $formAddTrick->isValid()) {

            $pictureFile =  $formAddTrick->get('picture')->getData();

            if ($pictureFile) {
                $originalFilename = $pictureFile;

                // // may have multiple additionnals pictures
                // $additionnalPictures = $formAddTrick->get('pictures')->getData();
                // foreach ($additionnalPictures as $additionnalPicture) {
                //     $file  = md5(uniqid()) . '.' . $additionnalPicture->guessExtension();
                //     $additionnalPicture->move(
                //         $this->getParameter('pictues_directory'),
                //         $file
                //     );
                //     // in db 
                //     $img = new Pictures();
                //     $img->setpicture($file);
                //     $tricks->addAdditionnalTrick($img);
                // }

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
     * function deleteAdditionnalPicture
     *
     * @param [type] $argument (trick id)
     * @return void
     */
    public function deleteAdditionnalPicture($argument)
    {
        // get the id of the trick
        $trickId = $argument; // $trickId = $trick->getId();
        // if this trick exist
        if ($this->tricksRepository->find($trickId) != null) {
            // get additional pictures list
            $additionnalPictures = [];
            $additionnalPictures = $this->picturesRepository->findBy(['tricks' => $trickId]);
            // if there is additionnal picture
            if ($additionnalPictures != []) {
                // may have multiple additionnals pictures
                foreach ($additionnalPictures as $additionnalPicture) {
                    $file = $additionnalPicture->getpicture();
                    // get the physical path
                    $additionalPictureWithPath = $this->getParameter('pictues_directory') . '/' .  $file;
                    // delete trick from server
                    if (file_exists($additionalPictureWithPath = $this->getParameter('pictues_directory') . '/' .  $file)) {
                        unlink($additionalPictureWithPath = $this->getParameter('pictues_directory') . '/' .  $file);
                    }
                } // end for each
            } else {
                // not additionnelpicture to drop
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
                    $img->setpicture($file);
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
}
