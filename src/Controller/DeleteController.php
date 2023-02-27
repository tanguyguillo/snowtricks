<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * 
 */
class DeleteController extends AbstractController
{
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
