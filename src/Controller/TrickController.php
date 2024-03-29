<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\User;
use App\Entity\Pictures;
use App\Entity\Comments;
use App\Entity\Movie;
use App\Repository\UserRepository;
use App\Repository\TricksRepository;
use App\Repository\PicturesRepository;
use App\Repository\CommentsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\TricksType;
use App\Form\AvatarType;
use App\Form\UpdateType;
use App\Form\CommentsType;
use App\Form\MovieType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use App\Controller\ServiceController;



/**
 * class TrickController
 * 
 */
#[Route('/tricks', name: 'tricks_')]
class TrickController extends AbstractController
{
    public $picturesRepository;
    public $tricksRepository;
    public $commentsRepository;
    public $serviceController;
    private $em;

    /**
     *  function __construct
     *
     * @param PicturesRepository $picturesRepository
     * @param TricksRepository $tricksRepository
     * @param EntityManagerInterface $em
     * @param CommentsRepository $commentsRepository
     * @param ServiceController $serviceController
     */
    public function __construct(PicturesRepository $picturesRepository, TricksRepository $tricksRepository, EntityManagerInterface $em, CommentsRepository $commentsRepository, ServiceController $serviceController)
    {
        $this->serviceController = $serviceController;
        $this->picturesRepository = $picturesRepository;
        $this->tricksRepository = $tricksRepository;
        $this->commentsRepository = $commentsRepository;
        $this->em = $em;
    }

    /**
     * function details (read)
     */
    #[Route('/details/{slug}', name: 'details')]
    public function details(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, Request $request, $slug, TricksRepository $tricksRepository, UserRepository $userRepository, Tricks $tricks, PicturesRepository $picturesRepository, CommentsRepository $CommentsRepository): Response
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);
        if (!$trick) {
            throw new NotFoundHttpException("No trick found");
        }

        $user = $this->getUser();
        if ($user != null) {
            $userId = $user->getId();
        } else {
            $userId = 0;
        }

        $author = $userRepository->findOneBy(['id' => $user]);
        $trickId = $trick->getId();
        $additionalPictures = $picturesRepository->findBy(['tricks' => $trickId]);

        $Image = $tricks->getPicture();
        $date = date('Y-m-d H:i:s');

        $comments = new Comments;
        $formComment = $this->createForm(CommentsType::class, $comments);
        $formComment->handleRequest($request);

        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('comment-item', $submittedToken)) {
            $comments->setContent($formComment->get('content')->getData());
            $comments->setRelation($trick);
            $comments->setUser($user);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($comments);
            $entityManager->flush();
            $this->addFlash('success', 'Your Comment have been added.');
            return $this->redirectToRoute('tricks_details', array('slug' => $slug));
        }

        $currentComments = $CommentsRepository->findBy(['relation' => $trickId], ['created_at' => 'desc']);

        return $this->render('tricks/details.html.twig', compact('trick', 'author', 'additionalPictures', 'Image', 'date', 'formComment', 'currentComments', 'userId'));  // 
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

        $authorId = $trick->getUser();
        $author = $userRepository->findOneBy(['id' => $authorId]);
        $trickId = $trick->getId();

        $additionalPictures = $picturesRepository->findBy(['tricks' => $trickId]);

        $image = $tricks->getPicture();

        $formUpdateTrick = $this->createForm(UpdateType::class, $trick);
        $formUpdateTrick->handleRequest($request);
        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('update' . $trick->getId(), $submittedToken)) {
            $trick->setContent($formUpdateTrick->get('content')->getData());
            $trick->setCategory($formUpdateTrick->get('category')->getData());
            $trick->setModifiedAt(new \DateTimeImmutable("now"));
            $pictureFile =  $formUpdateTrick->get('picture')->getData();

            $morePictures = $formUpdateTrick->get('pictures')->getData();


            if ($morePictures != []) {
                $this->serviceController->addAdditionalPicture($morePictures, $tricks);
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
                    $message = $this->addFlash('error', 'error type:' . $e);
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

        return $this->render('tricks/update.html.twig', compact('trick', 'author', 'additionalPictures', 'formUpdateTrick', 'image'));
    }

    /*
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

        $user = new User();
        $formAvatar = $this->createForm(AvatarType::class, $user);
        $formAvatar->handleRequest($request);
        $user = $this->getUser();
        $userId = $user->getId();

        $currentAvatar = $user->getAvatar();


        if ($formAddTrick->isSubmitted() && $formAddTrick->isValid()) {

            $pictureFile =  $formAddTrick->get('picture')->getData();

            if ($pictureFile) {
                $originalFilename = $pictureFile;
                $additionalPictures = $formAddTrick->get('pictures')->getData();

                $this->serviceController->addAdditionalPicture($additionalPictures, $tricks);

                $newFilename = md5(uniqid()) . '.' . $originalFilename->guessExtension();
                $tricks->setPicture($newFilename);
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $message = $this->addFlash('error', 'error type: ' . $e);
                    return $this->render('tricks/add.html.twig', [
                        'formAddTrick' =>  $formAddTrick->createView(),
                        'message' => $message
                    ]);
                }
                $tricks->setPicture($newFilename);
            }

            $video = $formAddTrick->get('videos')->getData();
            $movie = new Movie();
            $movie = $movie->setMovies($video);
            $tricks->addVideo($movie);

            $tricks->setUser($this->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($tricks);
            $entityManager->flush();
            $this->addFlash('success', 'Your trick have been added.');
            return $this->redirectToRoute('app_home');
        }

        if ($formAvatar->isSubmitted() && $formAvatar->isValid()) {
            $pictureFile =  $formAvatar->get('avatar')->getData();

            $originalFilename = $pictureFile;
            $originalFilenameWithPath = $this->getParameter('pictures_directory') . '/' . $user->getAvatar();
            $newFilename = md5(uniqid()) . '.' . $originalFilename->guessExtension();
            try {
                $pictureFile->move(
                    $this->getParameter('pictures_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $message = $this->addFlash('error', 'error type: ' . $e);
                return $this->render('tricks/add.html.twig', [
                    'formAddTrick' =>  $formAddTrick->createView(),
                    'message' => $message
                ]);
            }
            $user->setAvatar($newFilename);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->serviceController->deletePicture($originalFilenameWithPath);
            $this->addFlash('success', 'Your avatar have been added.');

            return $this->redirectToRoute('tricks_app_user_tricks_add');
        }


        return $this->render('tricks/add.html.twig', [
            'formAddTrick' =>  $formAddTrick->createView(),
            'formAvatar' =>   $formAvatar->createView(),
            'userId' => $userId,
            'currentAvatar' => $currentAvatar,
        ]);
    }

    /** 
     * Function write your first and last name (in user) in add a tricks page
     */
    #[Route('/memberName/{userId}', name: 'app_memberName')]
    public function firstAndLastName(int $userId, Request $request, ManagerRegistry $doctrine)
    {
        $user = $this->getUser();
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('FirstAndLastName' . $userId, $submittedToken)) {

            $firstName = htmlentities($request->request->get('firstName'));
            $lastName = htmlentities($request->request->get('lastName'));

            $user->setFirstName($firstName);
            $user->setLastName($lastName);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your name have been added or updated.');
        } else {
            $this->addFlash('error', "Your name don't have been added; try again.");
        }
        return $this->redirectToRoute('tricks_app_user_tricks_add');
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
            $this->serviceController->deleteAdditionalPicture($trickId);
            $mainPictureWithPath = $this->getParameter('pictures_directory') . '/' . $trick->getPicture();
            $tricksRepository->remove($trick, true);
            if ($this->serviceController->deletePicture($mainPictureWithPath)) {
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
            $this->serviceController->deleteAdditionalPicture($trickId);
            $mainPictureWithPath = $this->getParameter('pictures_directory') . '/' . $trick->getPicture();
            $tricksRepository->remove($trick, true);
            if ($this->serviceController->deletePicture($mainPictureWithPath)) {
                $this->addFlash('success', 'Your trick have been deleted.');
            } else {
                $this->addFlash('error', 'Something goes wrong.');
            }
            return $this->redirectToRoute('app_home');
        }
    }

    /**
     * function to delete one additional picture by picture id from update screen
     *
     * @param [type]  $pictureId
     * @return void
     */
    #[Route('/delete-picture/{pictureId}', name: 'app_delete_picture', methods: ['DELETE'])]
    public function deleteOneAdditionalPicture(int $pictureId, Request $request)
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $pictureId, $submittedToken)) {

            $additionalPicture = $this->picturesRepository->find(array('id' => $pictureId));

            $file =  $additionalPicture->getPicture();
            $additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;
            if ($this->serviceController->deletePicture($additionalPictureWithPath)) {
                $this->em->remove($additionalPicture);
                $this->em->flush();
                return new JsonResponse("oui : additionalPictureDeleted", 200);
            } else {
                return new JsonResponse("non ", 500);
            }
        }
    }

    #[Route('/delete-main-picture-only/{trickId}', name: 'app_delete_main_only', methods: ['DELETE'])]
    public function deleteMainPictureOnly(int $trickId, Request $request)
    {
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $trickId, $submittedToken)) {
            $trick = $this->tricksRepository->findOneById($trickId);
            $file  = htmlentities($trick->getPicture());
            $PictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;
            if ($this->serviceController->deletePicture($PictureWithPath)) {
                return new JsonResponse("oui : additionalPictureDeleted", 200);
            } else {
                return new JsonResponse("non ", 500);
            }
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
}
