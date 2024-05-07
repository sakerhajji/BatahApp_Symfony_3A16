<?php

namespace App\Controller\UtlisateurControllers;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test') ]
    public function index(SessionInterface $session, MailerInterface $mailer , UtilisateurRepository $repository ): Response
    {
//        $emailSender = new EmailSender();
//        $emailSender->sendEmail();


        $data=$session->get('user') ;
        return $this->render('utilisateur/csv_upload.html.twig', [
            'user'=>$data ,
        ]);

    }







}
