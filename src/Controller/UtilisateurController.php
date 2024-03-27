<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/utilisateur')]
class UtilisateurController extends AbstractController
{

    #[Route('/ajouter', name: 'ajouter', methods: ['POST'])]

    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = $request->request->all();
        $utilisateur =new Utilisateur();
        $utilisateur->setNomutilisateur($data['first_name']);
        $utilisateur->setPrenomutilisateur($data['last_name']);
        $utilisateur->setAdresseemail($data['email']);
        $utilisateur->setDatedenaissance(new \DateTime($data['date_de_naissance']));
        $utilisateur->setSexe($data['gender']);
        $utilisateur->setMotdepasse($data['password']);
        // Instead of passing the user as a parameter, use the EntityManager to persist and flush
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        // Redirect or return a response
        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);

    }
    #[Route('/Iscription', name: 'Iscription', methods: ['POST'])]

    public function Iscription (Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = $request->request->all();
        $utilisateur =new Utilisateur();
        $utilisateur->setNomutilisateur($data['first_name']);
        $utilisateur->setPrenomutilisateur($data['last_name']);
        $utilisateur->setAdresseemail($data['email']);
        $utilisateur->setDatedenaissance(new \DateTime($data['date_de_naissance']));
        $utilisateur->setSexe($data['gender']);
        $utilisateur->setMotdepasse($data['password']);
        // Instead of passing the user as a parameter, use the EntityManager to persist and flush
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        // Redirect or return a response
        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);

    }
    #[Route('/Login', name: 'Login', methods: ['POST'])]

    public function Login (Request $request, EntityManagerInterface $entityManager , UtilisateurRepository $repository): Response
    {

        $data = $request->request->all();
        $utilisateur =new Utilisateur();
        $utilisateur->setAdresseemail($data['email']);
        $utilisateur->setMotdepasse($data['password']);
        $utilisateur=$repository->login($utilisateur->getAdresseemail(),$utilisateur->getMotdepasse()) ;
     if($utilisateur != null)
     return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        else
            dd("password or mail incorrect") ;


    }
    #[Route('/ForgetPassword', name: 'ForgetPassword', methods: ['POST'])]

    public function ForgetPassword (Request $request, EntityManagerInterface $entityManager , UtilisateurRepository $repository , SessionInterface $session): Response
    {

        $data = $request->request->all();
        $utilisateur =new Utilisateur();
        $utilisateur->setAdresseemail($data['email']);
        $utilisateur=$repository->ForgetPassword($utilisateur->getAdresseemail()) ;
        if($utilisateur != null)
            //return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        {   $session->set('user',$utilisateur) ;
            dd($session->get('user')) ;

        }
            else
            dd("mail n'existe pas") ;


    }

    #[Route('/', name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }






}
