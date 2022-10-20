<?php

// src/Controller/SecurityController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

//use App\Controller\MailerController;

/**
 * class  registration  / login / log out / generateToken
 */
class SecurityController extends AbstractController
{

    public $mailer;

    public function __construct()
    {
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return void
     */
    #[Route('/registration', name:'app_security_registration', methods:['POST', 'GET'])]
function registrationPost(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer)
    {
    $user = new User();
    $form = $this->createForm(RegistrationType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $plaintextPassword = $form->get('passwordUser')->getData();
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );

        $last_username = $user->getUsername();
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        $user->setToken(strtr(base64_encode(random_bytes(32)), '+/', '-%'), '='); // to in a other way....
        $user->setDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        // EmailValidation  ->to($user->getEmailUser())
        $email = (new Email())
            ->from('snowtricks')
            ->to('tanguy.guillo@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        return $this->redirectToRoute('home');
    }
    return $this->renderForm('security/registration.html.twig', ['form' => $form]);
}
/***
 * Only to log out
 */
#[Route('/logout', name:'app_logout', methods:['GET'])]
function logout()
    {
    // controller can be blank: it will never be called!
    throw new \Exception('Don\'t forget to activate logout in security.yaml');
}

/**
 *  function  https: //symfony.com/doc/current/security/login_link.html

 *
 * @return void
 */
#[Route('/login_check', name:'login_check')]
function check()
    {
    throw new \LogicException('This code should never be reached');
}

#[Route('/login2', name:'login2')]
function requestLoginLink(LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository, Request $request)
    {

    var_dump('1');

    // check if login form is submitted
    if ($request->isMethod('POST')) {

        var_dump('2');

        exit;

        // load the user in some way (e.g. using the form input)
        $email = $request->request->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);

        // create a login link for $user this returns an instance
        // of LoginLinkDetails
        $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
        $loginLink = $loginLinkDetails->getUrl();

        // ... send the link and return a response (see next section)
    }

    // if it's not submitted, render the "login" form
    return $this->render('security/login.html.twig');
}

}
