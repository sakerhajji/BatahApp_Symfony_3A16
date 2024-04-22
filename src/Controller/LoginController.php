<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;


class LoginController extends AbstractController
{
    #[Route('/loginPage', name: 'app_login' ,methods: ['GET','POST'])]
    public function index(Request $request ): Response
    {

        $errorMsg=$request->get('errorMsg') ;
        return $this->render('login/loginPage.html.twig', [
            'errorMsg' => $errorMsg,
        ]);
    }

    #[Route('/Login', name: 'Login', methods: ['POST'])]
    public function Login(Request $request, EntityManagerInterface $entityManager, UtilisateurRepository $repository , session $session): Response
    {

        $check = new InputControl();
        $data = $request->request->all();
        $utilisateur = new Utilisateur();
        $utilisateur->setAdresseemail($data['email']);
        $utilisateur->setMotdepasse($data['password']);
        $plainPassword= $data['password'] ;
        $utilisateur = $repository->login($utilisateur->getAdresseemail(), $utilisateur->getMotdepasse());

        if ($utilisateur != null && password_verify( $plainPassword , $utilisateur->getMotdepasse() )) {
            $session->set('user',$utilisateur) ;
            dd($session) ;
            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }
        else {
            $errorMsg = "Ecrire mail et motdepass valid svp";
            return $this->redirectToRoute('app_login', [
                'errorMsg' => $errorMsg,
            ]);
        }
    }
}
