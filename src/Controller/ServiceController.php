<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use App\Entity\Tricks;
use App\Repository\TricksRepository;
use App\Repository\PicturesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

/**
 * class
 */

/**
 * class DeleteController
 */
class ServiceController extends AbstractController
{
    public $picturesRepository;
    public $tricksRepository;
    public $commentsRepository;
    public $deleteController;
    private $em;

    // */
    // -    #[Route('/delete-tricks_from_detail/{id}', name: 'app_tricks_delete_from_detail', methods: ['Post'])]
    // -    public function deleteFromDetail(Request $request, Tricks $trick, TricksRepository $tricksRepository)
    // +    #[Route('/details/modifications/{slug}', name: 'modifications')]
    // Slit the controller in smaller controller

    public function __construct(PicturesRepository $picturesRepository, TricksRepository $tricksRepository, EntityManagerInterface $em)
    {

        $this->picturesRepository = $picturesRepository;
        $this->tricksRepository = $tricksRepository;

        $this->em = $em;
    }

    /**
     * function delete Additional from Entity PicturesPicture 
     *
     * @param [type] $argument (trick id) :  all the additional pictures of a trick from the server
     * @return void
     */
    public function deleteAdditionalPicture($argument)
    {
        $trickId = $argument;
        if ($this->tricksRepository->find($trickId) != null) {
            $additionalPictures = [];
            $additionalPictures = $this->picturesRepository->findBy(['tricks' => $trickId]);
            if ($additionalPictures != []) {
                foreach ($additionalPictures as $additionalPicture) {
                    $file = $additionalPicture->getPicture();
                    $additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file;
                    if (file_exists($additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file)) {
                        unlink($additionalPictureWithPath = $this->getParameter('pictures_directory') . '/' .  $file);
                    }
                }
            }
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
    public function deletePicture($PictureWithPath)
    {
        if (file_exists($PictureWithPath)) {
            unlink($PictureWithPath);
            return 1;
        } else {
            return 0;
        }
    }
}
