<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class SecurityController extends AbstractController
{
#[Route('/registration', name: 'app_security_registration', methods: ['POST', 'GET'])]
public function registrationPost(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher)
{
    $user = new User();
    // binding fields
    $form = $this->createForm(RegistrationType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // hash the password (based on the security.yaml config for the $user class)

        //var_dump($user->getPasswordUser());  // string(24) "toto@toto.frtoto@toto.fr"

        //$hashedPassword = $password_hashers->hashPassword($user, $user->getPasswordUser());
        //$hashedPassword = $password_hashers->hashPassword($user, $user->getPasswordUser());

       //$user->setPasswordUser($hashedPassword);

        // necessary
        $user->setRoleUser('ROLE_USER');
        $user->setPictureUserUrl = "../assets/img/snowboard-background.png";

        $manager->persist($user);
        $manager->flush();

        //  $task->setDueDate(new \DateTime('tomorrow'));
    }

    return $this->renderForm('security/registration.html.twig', [
        'form' => $form,
    ]);
}

}