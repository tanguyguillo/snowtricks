<?php

// src/Controller/SecurityController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

//use App\Controller\MailerController;

/**
 * class  registration  / login / log out / generateToken
 */
class SecurityController extends AbstractController
{
    public $mailer;
    public $userRepository;
    public $email;
    private $user;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository; // injection
    }

    /**
     * function creating account and sending validation Email
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return void
     */
    #[Route('/registration', name:'app_security_registration', methods:['POST', 'GET'])]
function registrationPost(
    Request $request,
    EntityManagerInterface $manager,
    UserPasswordHasherInterface $passwordHasher,
    LoginLinkHandlerInterface $loginLinkHandler,
    NotifierInterface $notifier
) {
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

        // DB
        $last_username = $user->getUsername();
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        $user->setToken(strtr(base64_encode(random_bytes(32)), '+/', '-%'), '='); // to in a other way....
        $user->setDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        $this->user = $user;
        // other way link
        $email = $user->getEmailUser();
        $this->email = $email;
        $name = $user->getUsername();

        $test = 1;
        if ($test == 1) {
            $this->requestLoginLink($notifier, $loginLinkHandler, $this->userRepository, $request);
        }

        // // create a login link for $user this returns an instance
        // // of LoginLinkDetails
        // $email = $request->request->get('email');

        // $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
        // $loginLink = $loginLinkDetails->getUrl();

        // // create a login link for $user this returns an instance
        // // of LoginLinkDetails
        // $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
        // $loginLink = $loginLinkDetails->getUrl(); // OK
        // // for exemple : http: //127.0.0.1:8001/login_check?user=test3@test3.fr&expires=1666376910&hash=NDJmNGEyMjNiNjA4M2RjNmJmZGI5YTQxNDU1NjNmZDFiMTlhZDY4MmJkOTU1NWU3ZjBmNmVmYzYwMTllMzczMA%3D%3D

        // //echo ($loginLink);

        // // create a notification based on the login link details
        // $notification = new LoginLinkNotification(
        //     $loginLinkDetails,
        //     'Welcome to Snowtricks' // email subject
        // );

        // //dd($loginLinkDetails);  OK : http: //127.0.0.1:8000/login_check?user=rrrrrr@rr.fr&expi etc.....

        // // create a recipient for this user
        // $recipient = new Recipient($user->getEmailUser()); // OK

        // // send the notification to the user
        // $notifier->send($notification, $recipient); // OK  .. / render a "Login link is sent!" page

        return $this->redirectToRoute('home'); // or render a "Login link is sent!" page
    }
    return $this->renderForm('security/registration.html.twig', ['form' => $form]);
}
/***
 * Only to logOut
 * @return void
 */
#[Route('/logout', name:'app_logout', methods:['GET'])]
function logout()
    {
    // controller can be blank: it will never be called!
    throw new \Exception('Don\'t forget to activate logout in security.yaml');
}

#[Route('/login_check', name:'login_check')]
function check(Request $request)
    {
    throw new \LogicException('This code should never be reached');
}

#[Route('/login', name:'login')]
function requestLoginLink(
    NotifierInterface $notifier,
    LoginLinkHandlerInterface $loginLinkHandler,
    UserRepository $userRepository,
    Request $request) {

    if ($request->isMethod('POST')) {
        $email = $request->request->get('emailUser');
        $user = $userRepository->findOneBy(['emailUser' => $email]);

        $loginLinkDetails = $loginLinkHandler->createLoginLink($this->user);

        // create a notification based on the login link details
        $notification = new LoginLinkNotification(
            $loginLinkDetails,
            'Welcome on SnowTricks' // email subject
        );

        // create a recipient for this user
        $recipient = new Recipient($this->email);

        // send the notification to the user
        $notifier->send($notification, $recipient);

        // render a "Login link is sent!" page
        //return $this->render('security/login_link_sent.html.twig');

        return $this->redirectToRoute('home');

    }
    return $this->render('security/login.html.twig');
}
}
