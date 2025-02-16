<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailTestController extends AbstractController
{
    #[Route('/test-mail', name: 'test_mail')]
    public function sendTestMail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('noreply@tondomaine.com')
            ->to('212ivann.rambaud@gmail.com') // Mets une adresse réelle
            ->subject('Test Mail Symfony')
            ->text('Ceci est un test d’envoi d’email avec Symfony Mailer.')
            ->html('<p>Ceci est un test d’envoi d’email avec Symfony Mailer.</p>');

        $mailer->send($email);

        return new Response('Email envoyé !');
    }
}
