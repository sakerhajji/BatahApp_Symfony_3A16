<?php
namespace App\Controller\UtlisateurControllers;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $this->session->clear();
        $errorMsg = $request->get('errorMsg');
        return $this->render('login/loginPage.html.twig', [
            'errorMsg' => $errorMsg,
            'site_key' => $this->getParameter('recaptcha_site_key')
        ]);
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

