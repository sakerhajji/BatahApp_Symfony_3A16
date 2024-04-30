<?php

namespace App\Controller\UtlisateurControllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ExceptionController extends AbstractController
{
    #[Route('/exception', name: 'app_exception')]
    public function index(): Response
    {
        return $this->render('exception/index.html.twig', [
            'controller_name' => 'ExceptionController',
        ]);
    }

    public function show404(NotFoundHttpException $exception): Response
    {
        // You can render your custom 404 page here
        return $this->render('exception/404Page.html.twig');
    }
}
