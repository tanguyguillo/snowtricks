<?php

// src/Controller/MailerController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/email')]
function sendEmail(MailerInterface $mailer): Response
    {
    $email = (new Email())
        ->from('snowtricks@omegawebprod')
        ->to('tanguy.guillo@gmail.com')
        //->cc('tanguy.guillo@gmail.com')
        ->bcc('tanguy.guillo@gmail.com')
        //->replyTo('fabien@example.com')
        ->priority(Email::PRIORITY_HIGH)
        ->subject('Validation email of your inscription')
        ->text('Sending emails is fun again!')
        ->html('<p>See Twig integration for better HTML integration!</p>');

    $mailer->send($email);

    return $this->redirectToRoute('home'); // return home after inscription
}
}
