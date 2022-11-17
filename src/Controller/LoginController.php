<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * class login
 */
class LoginController extends AbstractController
{
#[Route('/login', name: 'security_login', methods: ['POST', 'GET'])] // name is here the name of the path
public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUserName = $authenticationUtils->getLastUserName();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUserName,
            'error'     => $error,
        ]);
    }
}