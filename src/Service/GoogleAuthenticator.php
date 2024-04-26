<?php
# src/Security/GoogleAuthenticator.php
namespace App\Security;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class GoogleAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;

    private UserRepository $usersRepository;
    public function __construct(private UrlGeneratorInterface $urlGenerator,ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router,UtilisateurRepository  $usersRepository)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->usersRepository = $usersRepository;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $email = $googleUser->getEmail();

                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                if (!$existingUser) {
                    $user = new User();
                    $user->setEmail($googleUser->getEmail());
                    $user->setPrenom($googleUser->getFirstName());
                    $user->setNom($googleUser->getLastName());

                    $imageUrl = $googleUser->getAvatar();

                    $directory = 'C:\Users\PC\Desktop\SymfonyFinFolio\public\imagesUser';

                    $imageContent = file_get_contents($imageUrl);

                    if ($imageContent === false) {
                        echo "Erreur lors du téléchargement de l'image.";
                        exit;
                    }

                    $randomFileName = uniqid() . '_' . time() . '.jpg';

                    $localFilePath = $directory . '/' . $randomFileName;

                    $result = file_put_contents($localFilePath, $imageContent);

                    if ($result === false) {
                        dd("habetch tetsab");
                    }
                    $user->setImage($randomFileName);
                    $user->setPassword(sha1("Ahmed+2002"));
                    $user->setAdresse("Tunisie");
                    $user->setNumtel("+21652327720");
                    $user->setStatut("active");
                    $user->setNbcredit(0);
                    $user->setRole("user");
                    $user->setSolde(2000);
                    $user->setRate(2);
                    $user->setDatepunition(new \DateTime('0000-00-00'));
                    $user->setTotalTax(0);
                    // $existingUser->setHostedDomain($googleUser->getHostedDomain());
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    return $user;
                }
//                $imageUrl = $googleUser->getAvatar();
//
//                $directory = 'C:\Users\PC\Desktop\SymfonyFinFolio\public\imagesUser';
//
//                $imageContent = file_get_contents($imageUrl);
//
//                if ($imageContent === false) {
//                    echo "Erreur lors du téléchargement de l'image.";
//                    exit;
//                }
//
//                $randomFileName = uniqid() . '_' . time() . '.jpg';
//
//                $localFilePath = $directory . '/' . $randomFileName;
//
//                $result = file_put_contents($localFilePath, $imageContent);
//
//                if ($result === false) {
//                    dd("habetch tetsab");
//                }
//                $existingUser->setImage($randomFileName);
//                $this->entityManager->flush();


                return $existingUser;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        $userIdentifier = $user->getUserIdentifier();
        $users = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
        if ($users->getRole() === "user") {
            return new RedirectResponse($this->urlGenerator->generate('app_user_index'));
        }
        if ($users->getRole() === "admin") {
            return new RedirectResponse($this->urlGenerator->generate('app_user_index'));
        }
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
        // change "app_dashboard" to some route in your app
        return new RedirectResponse(
            $this->router->generate('app_login')
        );

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}