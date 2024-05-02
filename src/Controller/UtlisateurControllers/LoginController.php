<?php

namespace App\Controller\UtlisateurControllers;

use App\Entity\Utilisateur;
use App\Form\LoginType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LoginController extends AbstractController
{
    private SessionInterface $session;
    private HttpClientInterface $client;  // Inject HttpClient

    public function __construct(SessionInterface $session, HttpClientInterface $client)
    {
        $this->session = $session;
        $this->client = $client;
    }

    #[Route('/loginPage', name: 'app_login', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {

        $errorMsg = $request->get('errorMsg');
        return $this->render('login/loginPage.html.twig', [
            'errorMsg' => $errorMsg,
            'site_key' => $this->getParameter('recaptcha_site_key')
        ]);
        return $this->redirectToRoute('app_utilisateur_index');

        /*
        $utilisateur = new Utilisateur();
        $form = $this->createForm(LoginType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $email = $form->get('adresseemail')->getData();

            // get the user from the database
            $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
            $user = $repository->findOneBy(['adresseemail' => $email]);


            if ($user) {
                // User found, set the user in the session
                $session = new Session();
                $session->set('user', $user);

                if ($user->getRole() == "U") {
                    // handle the login
                    return $this->redirectToRoute('app_afficahge_produits');
                } else {
                    // handle the login
                    return $this->redirectToRoute('app_back_affiche');
                }
            } else {
                // User not found, authentication failed
                return $this->redirectToRoute('login_failure');
            }
        }

        return $this->render('home/login.html.twig', [
            'form' => $form->createView(),
        ]);
    */
    }

    #[Route('/deconnection', name: 'app_logout', methods: ['GET'])]
    public function logout(): Response
    {
        $this->session->clear();
        return $this->redirectToRoute('app_login');
    }

    #[Route('/Login', name: 'Login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, UtilisateurRepository $repository): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $recaptchaResponse = $request->request->get('g-recaptcha-response');
        $remoteIp = $request->getClientIp();
        $secret = $this->getParameter('recaptcha_secret_key');

        if (!$recaptchaResponse) {
            return $this->redirectToRoute('app_login', ['errorMsg' => 'reCAPTCHA response not found.']);
        }

        $response = $this->client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $secret,
                'response' => $recaptchaResponse,
                'remoteip' => $remoteIp
            ]
        ]);

        $result = $response->toArray();
        if (!($result['success'] ?? false)) {
            return $this->redirectToRoute('app_login', ['errorMsg' => 'Invalid reCAPTCHA.']);
        }


        $user = $repository->findOneBy(['adresseemail' => $email]);


        if ($user && password_verify($password, $user->getMotdepasse())) {
            $this->session->set('user', $user);
            return $this->redirectToRoute('app_utilisateur_index');
        }

        return $this->redirectToRoute('app_login', ['errorMsg' => 'Invalid email or password.']);
    }
}
