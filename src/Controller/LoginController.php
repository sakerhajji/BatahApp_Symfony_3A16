<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class LoginController extends AbstractController
{
    private   $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    #[Route('/loginPage', name: 'app_login' ,methods: ['GET','POST'])]
    public function index(Request $request ): Response
    {

        $errorMsg=$request->get('errorMsg') ;
        return $this->render('login/loginPage.html.twig', [
            'errorMsg' => $errorMsg,
        ]);
    }
    #[Route('/deconnection', name: 'app_logout', methods: ['GET'])]
    public function logout(): Response
    {
        // Clear the session
        $this->session->clear();

        // Redirect to the login page
        return $this->redirectToRoute('app_login');
    }

    #[Route('/Login', name: 'Login', methods: ['POST'])]
    public function Login(Request $request, EntityManagerInterface $entityManager, UtilisateurRepository $repository ): Response
    {

        $check = new InputControl();
        $data = $request->request->all();
        $utilisateur = new Utilisateur();
        $utilisateur->setAdresseemail($data['email']);
        $utilisateur->setMotdepasse($data['password']);
        $plainPassword= $data['password'] ;
        $utilisateur = $repository->login($utilisateur->getAdresseemail(), $utilisateur->getMotdepasse());

        if ($utilisateur != null && password_verify( $plainPassword , $utilisateur->getMotdepasse() )) {

            $this->session->set('user',$utilisateur) ;
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
