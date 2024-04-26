<?php

namespace App\Controller;

use App\Repository\AvisLivraisonRepository;
use App\Repository\LivraisonRepository;
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
    #[Route('/front', name: 'app_test1')]
    public function indexfront(PartenairesRepository $PartenairesRepository,AvisLivraisonRepository $avisLivraisonRepository): Response
    {
        $partenaires = $PartenairesRepository->findAll();
        $avis=$avisLivraisonRepository->findAll();
       return $this->render('front.html.twig',[
           'partenaires' => $partenaires,
           'avis' =>$avis
       ]);
    }
}
