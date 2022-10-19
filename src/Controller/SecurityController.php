<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
/**
 * class  registration  / login /
 */
class SecurityController extends AbstractController
{

    
#[Route('/registration', name: 'app_security_registration', methods: ['POST', 'GET'])]
public function registrationPost(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher)
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

        // $this->generateToken();
        // >setDate(new $Creation_date = \DateTime());

        // necessary for interface
        $user->setRoles(['ROLE_USER']);

        // necessary for me
        $user->setPictureUserUrl = "../assets/img/snowboard-background.png"; // picture by defahlt
        //  $task->setDueDate(new \DateTime('tomorrow')); mahage date for suppression
        // token
        // confirmed

        //$submittedToken = $request->request->get('token'); // 

        // $p = new \OAuthProvider();
        // $t = $p->generateToken(12);

        // $token = JsonResponse(['token' => $JWTManager->create($user)]);

        // dd($token);

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
#[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
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