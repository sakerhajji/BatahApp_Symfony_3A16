<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SignupController extends AbstractController
{

    #[Route('/signUp', name: 'app_signup', methods: ['GET', 'POST'])]
    public function index(request $request): Response
    {
        $recaptchaSiteKey = $this->getParameter('recaptcha.site_key');
        $errorMsg = $request->get('errorMsg');
        return $this->render('signup/signUp.html.twig', [
            'errorMsg' => $errorMsg,
            'recaptcha_site_key' => $recaptchaSiteKey ,
        ]);

    }

    #[Route('/Iscription', name: 'Iscription', methods: ['POST'])]
    public function Iscription(Request $request, EntityManagerInterface $entityManager ,UtilisateurRepository $repository , SessionInterface $session): Response
    {

        $errorMsg = "Invalide formulaire d'inscription. Veuillez vérifier vos informations.";
        $check = new InputControl();
        $data = $request->request->all();

        // Find the Utilisateur object by its identifier
        $utilisateur = new Utilisateur();


        // Proceed with your logic if the Utilisateur object exists
        $utilisateur->setNomutilisateur($data['first_name']);
        $utilisateur->setPrenomutilisateur($data['last_name']);
        $utilisateur->setAdresseemail($data['email']);
        $utilisateur->setDatedenaissance(new \DateTime($data['date_de_naissance']));
        $utilisateur->setSexe($data['gender']);
        $utilisateur->setMotdepasse($data['password']);
        $checkMail=$repository->findBy(['adresseemail'=>$utilisateur->getAdresseemail()]) ;

        if ($check->checkPasswordStrength($utilisateur->getMotdepasse()) &&
            $check->verifierNom($utilisateur->getNomutilisateur()) &&
            $check->verifierNom($utilisateur->getPrenomutilisateur()) &&
            $check->verifyEmail($utilisateur->getAdresseemail())&&
            $utilisateur->getMotdepasse()==$data['confirm_password']&&
            empty($checkMail)

        ) {
            $utilisateur->setMotdepasse( password_hash($utilisateur->getMotdepasse(), PASSWORD_BCRYPT));
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            $session->set('user',$utilisateur) ;

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        } else {


            return $this->redirectToRoute('app_signup', [
                'errorMsg' => $errorMsg,

            ],Response::HTTP_SEE_OTHER );
        }
    }

}
