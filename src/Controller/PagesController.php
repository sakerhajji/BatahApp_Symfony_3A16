<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(SessionInterface $session): Response
    {
        return $this->render('base.html.twig');
    }


    #[Route('/forgetpassword', name: 'forgetpassword')]
    public function forgetpassword(): Response
    {
        return $this->render('utilisateur/forgetpassword.html.twig');
    }


}
