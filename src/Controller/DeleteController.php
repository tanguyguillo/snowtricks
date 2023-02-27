<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * 
 */
class DeleteController extends AbstractController
{
    // */
    // -    #[Route('/delete-tricks_from_detail/{id}', name: 'app_tricks_delete_from_detail', methods: ['Post'])]
    // -    public function deleteFromDetail(Request $request, Tricks $trick, TricksRepository $tricksRepository)
    // +    #[Route('/details/modifications/{slug}', name: 'modifications')]
    // Slit the controller in smaller controller


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
