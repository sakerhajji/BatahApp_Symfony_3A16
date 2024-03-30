<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Session\Session;

class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('home/home.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {

        $utilisateur = new Utilisateur();
        $form = $this->createForm(LoginType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $email = $form->get('adresseemail')->getData();

            // get the user from the database
            $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
            $user = $repository->findOneBy(['adresseemail' => $email]);

            $session = new Session();
            $session->set('user', $user);

            if ($user->getRole() == "U") {
                // handle the login
                return $this->redirectToRoute('app_afficahge_produits');
            } else {
                // handle the login
                return $this->redirectToRoute('app_back_affiche');
            }
        }

        return $this->render('main/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register', name: 'app_ajouter')]
    public function indexAjouter(Request $request): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('main/register.html.twig', [
            'f' => $form->createView()
        ]);
    }
}
