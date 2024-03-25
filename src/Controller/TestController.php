<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/signUp', name: 'signUp')]
    public function signUp(): Response
    {
        return $this->render('utilisateur/loginPage.html.twig');
    }

    #[Route('/home', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
}
