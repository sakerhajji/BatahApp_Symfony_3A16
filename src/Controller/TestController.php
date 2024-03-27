<?php

namespace App\Controller;

use App\Services\EmailSender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/mail', name: 'mail')]
    public function index(SessionInterface $session , MailerInterface $mailer): Response
    {
        $emailSender = new EmailSender();
        $emailSender->sendEmail();
        return $this->render('base.html.twig');

    }



}
