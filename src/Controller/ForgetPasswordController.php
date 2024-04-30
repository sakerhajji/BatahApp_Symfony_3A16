<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\EmailSender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forget-password', name: 'forget_password_')]
class ForgetPasswordController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('utilisateur/forgetPassword.html.twig');
    }

    #[Route('/resive', name: 'resive', methods: ['POST'])]
    public function resivecode(Request $request, UtilisateurRepository $repository, SessionInterface $session): Response
    {
        $data = $request->request->all();
        $code = $data['code'] ?? null;

        if ($code === $session->get('code')) {
            return $this->render('utilisateur/newPassword.html.twig');
        } else {
            return $this->render('utilisateur/forgetPassword.html.twig');
        }
    }

    #[Route('/reset', name: 'reset_App', methods: ['POST'])]
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
        $newPasswordConfirmation = $data['cpassword'];
        $user = $session->get('user');

        if ($newPassword !== $newPasswordConfirmation) {
            $this->addFlash('error', 'Passwords do not match.');
            return $this->redirectToRoute('your_form_route_name', ['userId' => $user->getId()]);
        }

        $affectedRows = $repository->updatePasswordDQL($user->getId(), $newPassword);

        if ($affectedRows === 0) {
            $this->addFlash('error', 'No user found or password unchanged');
            return $this->redirectToRoute('your_form_route_name', ['userId' => $user->getId()]);
        }

        $this->addFlash('success', 'Password updated successfully');
        return $this->redirectToRoute('some_success_route');
    }
}
