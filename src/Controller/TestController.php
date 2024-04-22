<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/mail', name: 'mail') ]
    public function index(SessionInterface $session, MailerInterface $mailer , UtilisateurRepository $repository): Response
    {
//        $emailSender = new EmailSender();
//        $emailSender->sendEmail();


        return $this->render('utilisateur/profile.html.twig');

    }


}
