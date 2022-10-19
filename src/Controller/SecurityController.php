<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class  registration  / login / log out / generateToken
 */
class SecurityController extends AbstractController
{

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
function registrationPost(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher)
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
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        $user->setToken($this->generateToken()); // to in a other way....
        $user->setDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute('home'); // return home after inscription
        //return $this->redirectToRoute('app_security_login'); // another way to do go back login
    }
    //else show me the form
    return $this->renderForm('security/registration.html.twig', [
        'form' => $form,
    ]);
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
 * function token .... to review
 *
 * @return void
 */
function generateToken()
    {
    return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-%'), '=');
}

// #[Route('/login', name: 'security_login')] // name is here he name of the rooute
// public function login(AuthenticationUtils $authenticationUtils): Response
// {
//     // get the login error if there is one
//     $error = $authenticationUtils->getLastAuthenticationError();

//     // last username entered by the user
//     $lastUsername = $authenticationUtils->getLastUsername();

//     return $this->render('security/login.html.twig', [
//         'last_username' => $lastUsername,
//         'error'         => $error,
//     ]);
// }

}
