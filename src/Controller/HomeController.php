<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class Home page
 */
class HomeController extends AbstractController
{

    public $medias;
    /**
     *  function injection mediaRepository
     *
     * @param MediaRepository $mediaRepository
     */
    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    /**
     *  function home
     *
     * @return Response
     */
    #[Route('/', name:'home')]
    function home(): Response
    {

   //$medias = $this->mediaRepository->findAll(); // by date by default : array
    //$medias = array($medias);

    //dd($medias);

    // dd($medias[][0]);

    $title = "Snowtricks";

    return $this->render('snow_tricks/home.html.twig', compact('title'));
}

/**
 * function to return to "/" if home is taped
 *
 * @return Response
 */
#[Route('/home', name:'homeReturn')]
function homeReturn(): Response
    {
    return $this->redirectToRoute('home');
}
/**
 * function to string
 *
 * @return string
 */
// public function __toString(){
//     return $this->mediaRepository;
// }

}
