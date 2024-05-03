<?php

namespace App\Controller\ProduitControllers;


use App\Form\EncheresType;
use App\Entity\Encheres;
use App\Repository\EncheresRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EnchereController extends AbstractController
{
    #[Route('/enchere', name: 'app_enchere')]
    public function index(): Response
    {
        return $this->render('enchere/index.html.twig', [
            'controller_name' => 'EnchereController',
        ]);
    }
    #[Route('/add', name: 'app_Add_enchere')]
    public function add(Request $request): Response
    {
        $encheres = new Encheres();
        $encheres->setNbrParticipants(0);
        $form = $this->createForm(EncheresType::class, $encheres);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($encheres);
            $entityManager->flush();

            return $this->redirectToRoute('app_Affiche_enchere');
        }

        return $this->render('enchere/page-dashboard-add-encheres.html.twig', [
            'f' => $form->createView(),
        ]);
    }


    #[Route('/affiche', name: 'app_Affiche_enchere')]
    public function affiche(Request $request, EncheresRepository $encheresRepository): Response
    {
        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 5;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $searchQuery = $request->query->get('search', '');

        if ($searchQuery !== '') {
            $encheresItems = $encheresRepository->findBySearchQuery($searchQuery, $itemsPerPage, $offset);
            $totalItems = count($encheresItems);
        } else {
            $encheresItems = $encheresRepository->findBy([], null, $itemsPerPage, $offset);
            $totalItems = $encheresRepository->count([]);
        }

        $totalPages = ceil($totalItems / $itemsPerPage);

        return $this->render('enchere/page-dashboard-listing.html.twig', [
            'event' => $encheresItems,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }
}
