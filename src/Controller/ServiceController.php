<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Tricks;
use App\Entity\Pictures;
use App\Repository\TricksRepository;
use App\Repository\PicturesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

/**
 * class ServiceController
 */
class ServiceController extends AbstractController
{
    public $picturesRepository;
    public $tricksRepository;
    public $commentsRepository;
    private $em;

    /**
     * __construct function
     *
     * @param PicturesRepository $picturesRepository
     * @param TricksRepository $tricksRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(PicturesRepository $picturesRepository, TricksRepository $tricksRepository, EntityManagerInterface $em)
    {
        $this->picturesRepository = $picturesRepository;
        $this->tricksRepository = $tricksRepository;
        $this->em = $em;
    }

    /**
     * function to add additional picture
     *
     * @return void
     */
    public function addAdditionalPicture($additionalPictures, $tricks)
    {
        foreach ($additionalPictures as $additionalPicture) {
            $file  = md5(uniqid()) . '.' . $additionalPicture->guessExtension();
            $additionalPicture->move(
                $this->getParameter('pictures_directory'),
                $file
            );
            $img = new Pictures();
            $img->setPicture($file);
            $tricks->addAdditionalTrick($img);
        }
    }

    /************************************************
     *  function set picture empty for main picture - not used - to delete
     *
     * @param string $fileName
     * @param [type] $trickId
     * @return void
     */
    // public function setEmpty(string $fileName, $trickId)
    // {
    //     $trick = $this->tricksRepository->findOneById($trickId);
    //     $trick->setPicture($fileName);
    // }

    /**
     * function delete Additional from Entity PicturesPicture - $argument (trick id) :  all the additional pictures of a trick from the server
     *
     * @param [type] $argument
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
     * the  adding picture in trick on server - string (path of picture to delete on server) 
     *
     * @param [type]  string
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
