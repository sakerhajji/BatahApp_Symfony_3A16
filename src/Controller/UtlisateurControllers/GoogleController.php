<?php

# Controller/GoogleController
namespace App\Controller\UtlisateurControllers;

use App\Security\GoogleAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use MongoDB\Driver\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{

    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        //Redirect to google
        return $clientRegistry->getClient('google')->redirect([], []);
    }

    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     */
    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(Request $request, GoogleAuthenticator $authenticator , SessionInterface $session)
    {
        // Authenticate the user using the GoogleAuthenticator
        $user = $authenticator->authenticate($request);
        // Redirect to the homepage or a protected page
        $session->clear() ;
        $session->set('user',$user) ;
        if ($user->getNbrpoint()===-1)
        {
            return $this->render('utilisateur/newPassword.html.twig');
        }
        return $this->redirectToRoute('app_utilisateur_index');
    }

}