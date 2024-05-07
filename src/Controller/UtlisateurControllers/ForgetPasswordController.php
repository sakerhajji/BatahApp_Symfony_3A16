<?php

namespace App\Controller\UtlisateurControllers;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\EmailSender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class ForgetPasswordController extends AbstractController
{
    #[Route('/forget-password', name: 'forget_password_' , methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('utilisateur/forgetPassword.html.twig');
    }

    #[Route('/resive', name: 'resive', methods: ['POST'])]
    public function resivecode(Request $request, UtilisateurRepository $repository, SessionInterface $session): Response
    {
        $data = $request->request->all();
        $code = $data['code'] ?? null;
        $codeSession=$session->get('code') ;

        if ($code == $codeSession) {
            return $this->render('utilisateur/newPassword.html.twig');
        } else {
            return $this->render('utilisateur/forgetPassword.html.twig');
        }
    }

    #[Route('/reset', name: 'reset', methods: ['POST'])]
    public function reset(Request $request, UtilisateurRepository $repository, SessionInterface $session): Response

    {
        $data = $request->request->all();

        $email = $data['email'] ?? null;

        if (!$email) {
            // Assuming you have a flash message system or similar to notify the user
            $this->addFlash('error', 'No email provided.');
            return $this->redirectToRoute('forget_password_');
        }
        $utilisateur=new Utilisateur();
        $utilisateur=$repository->ForgetPassword($email) ;

        if ($utilisateur == null ) {
            $this->addFlash('error', 'Email does not exist.');
            return $this->render('utilisateur/forgetPassword.html.twig');
        }

        $randomNumber = rand(1000, 9999);
        $session->set('code', $randomNumber);
        $session->set('user',$utilisateur);


        $message = $randomNumber ;

        $emailSender = new EmailSender() ;
        $emailSender->sendEmail("saker.hajji13@gmail.com", "[Reset Password]", $message);

        $this->addFlash('success', 'A reset code has been sent to your email.');
        return $this->redirectToRoute('resive');

    }

    #[Route('/update-password', name: 'update_password', methods: ['POST'])]
    public function updatePassword(Request $request, UtilisateurRepository $repository, SessionInterface $session): Response
    {
        $data = $request->request->all();
        $newPassword = $data['password'];
        $newPasswordConfirmation = $data['confirm_password'];
        $user = $session->get('user');

        if ($newPassword !== $newPasswordConfirmation) {
            return $this->redirectToRoute('update_password');
        }
$repository=
        $affectedRows = $repository->updatePasswor($user->getId(), $newPassword);

        return $this->redirectToRoute('app_login');
    }
}
