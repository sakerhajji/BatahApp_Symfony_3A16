<?php

namespace App\Controller\EncheresController;

use App\Repository\EncheresRepository\PartenairesRepository;
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
