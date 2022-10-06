<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
//use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SecurityController extends AbstractController
{
/**
 * 
 */
    #[Route('/registration', name: 'app_security_registration')]
    //public function registration(Request $request, ObjectManager  $manager)
    public function registration(Request $request, EntityManagerInterface $manager)
    {
        $user = new User(); 

        // binding fields
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();
        }

        return $this->renderForm('security/registration.html.twig', [
            'form' => $form,
        ]);
    }
}