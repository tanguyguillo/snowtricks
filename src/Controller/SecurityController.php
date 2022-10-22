<?php

// src/Controller/SecurityController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
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

    public function __construct()
    {
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

        // other way link
        $email = $request->request->get('email');
        $name = $user->getUsername();

        // create a login link for $user this returns an instance
        // of LoginLinkDetails
        $email = $request->request->get('email');

        $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
        $loginLink = $loginLinkDetails->getUrl();

        // create a login link for $user this returns an instance
        // of LoginLinkDetails
        $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
        $loginLink = $loginLinkDetails->getUrl(); // OK
        // for exemple : http: //127.0.0.1:8001/login_check?user=test3@test3.fr&expires=1666376910&hash=NDJmNGEyMjNiNjA4M2RjNmJmZGI5YTQxNDU1NjNmZDFiMTlhZDY4MmJkOTU1NWU3ZjBmNmVmYzYwMTllMzczMA%3D%3D

        dd($loginLink);

        // create a notification based on the login link details
        $notification = new LoginLinkNotification(
            $loginLinkDetails,
            'Welcome to Snoxtricks' // email subject
        );
        // create a recipient for this user
        $recipient = new Recipient($user->getEmailUser());

        // send the notification to the user
        $notifier->send($notification, $recipient);

        // Oher way //
        //EmailValidation  ->to($user->getEmailUser())
        // $email = (new Email())
        //     ->from('tanguy.guillo@gmail.com')
        //     ->to('tanguy.guillo@gmail.com')
        //     ->subject('Time for Symfony Mailer!')
        //     ->text('Sending emails is fun again!')
        //     ->html('<p>See Twig integration for better HTML integration!</p>');
        // $mailer->send($email);

        return $this->redirectToRoute('home');
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
    // get the login link query parameters
    $expires = $request->query->get('expires');
    $username = $request->query->get('user');

    dd($username);

    $hash = $request->query->get('hash');

    // and render a template with the button
    return $this->render('security/process_login_link.html.twig', [
        'expires' => $expires,
        'user' => $username,
        'hash' => $hash,
    ]);
}

}
