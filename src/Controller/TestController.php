<?php

namespace App\Controller;

use App\Repository\PartenairesRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TestController extends AbstractController
{

    public function __construct(private PartenairesRepository $repository)
    {
    }

    #[Route('/home', name: 'app_test')]
    public function index(): Response
    {
       return $this->render('base.html.twig');
 //       dd($this->repository->findAll()) ;
    }
}
