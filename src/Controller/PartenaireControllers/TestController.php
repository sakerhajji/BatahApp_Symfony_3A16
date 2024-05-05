<?php

namespace App\Controller\PartenaireControllers;

use App\Repository\AvisLivraisonRepository;
use App\Repository\PartenairesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class TestController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    #[Route('/home', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('base.html.twig');
        //       dd($this->repository->findAll()) ;
    }

    #[Route('/front', name: 'app_test1')]
    public function indexfront(PartenairesRepository $PartenairesRepository, AvisLivraisonRepository $avisLivraisonRepository): Response
    {
        $partenaires = $PartenairesRepository->findAll();
        $avis = $avisLivraisonRepository->findAll();
        return $this->render('test/indexFront.html.twig', [
            'partenaires' => $partenaires,
            'avis' => $avis,
            'user' => $this->session->get('user'),

        ]);
    }
}
