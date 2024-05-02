<?php
# src/service/GoogleAuthenticator.php


namespace App\Security;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;





class GoogleAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private string $imagesDirectory;


    public function __construct(private UrlGeneratorInterface $urlGenerator, ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router, UtilisateurRepository $usersRepository)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;

    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Utilisateur
    {

        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);
        $googleUser = $client->fetchUserFromToken($accessToken);
        $user = new Utilisateur();
        $user->setIdgoogle($googleUser->getId());
        $user->setAdresseemail($googleUser->getEmail());
        $user->setNomutilisateur($googleUser->getFirstName());
        $user->setPrenomutilisateur($googleUser->getLastName());


        $existingUser = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['adresseemail' => $user->getAdresseemail()]);
         if (!$existingUser)
         {
             $this->entityManager->persist($user);
             $this->entityManager->flush();
         }
        $existingUser = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['adresseemail' => $user->getAdresseemail()]);


         return $existingUser;


    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        $userIdentifier = $user->getUserIdentifier();
        $users = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $userIdentifier]);
        return new RedirectResponse($this->urlGenerator->generate('app_login'));


    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    private function downloadAndSetUserAvatar(string $imageUrl): string
    {
        $imageContent = file_get_contents($imageUrl);
        if ($imageContent === false) {
            throw new \Exception("Failed to download image.");
        }

        $safeFilename = uniqid(); // Could also slugify the user's name if preferred
        $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
        $newFilename = $safeFilename . '.' . $extension;

        $tempImage = tmpfile();
        fwrite($tempImage, $imageContent);
        $metaDatas = stream_get_meta_data($tempImage);
        $tmpFilename = $metaDatas['uri'];

        $file = new File($tmpFilename);
        $file->move($this->imagesDirectory, $newFilename);

        if (isset($tempImage)) {
            fclose($tempImage);
        }

        return $newFilename;
    }
}