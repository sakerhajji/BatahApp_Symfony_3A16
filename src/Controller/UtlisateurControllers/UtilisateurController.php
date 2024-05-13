<?php

namespace App\Controller\UtlisateurControllers;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\EncheresRepository;
use App\Repository\LivraisonRepository;
use App\Repository\LocationRepository;
use App\Repository\PartenairesRepository;
use App\Repository\ProduitsRepository;
use App\Repository\UtilisateurRepository;
use App\Service\EmailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\String\ByteString;

#[Route('/utilisateur')]
class UtilisateurController extends AbstractController


{
    private   $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }



    #[Route('/ajouter', name: 'ajouter', methods: ['POST'])]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {

        $data = $request->request->all();
        $utilisateur = new Utilisateur();
        $utilisateur->setNomutilisateur($data['first_name']);
        $utilisateur->setPrenomutilisateur($data['last_name']);
        $utilisateur->setAdresseemail($data['email']);
        $utilisateur->setDatedenaissance(new \DateTime($data['date_de_naissance']));
        $utilisateur->setSexe($data['gender']);
        $utilisateur->setMotdepasse($data['password']);
        // Instead of passing the user as a parameter, use the EntityManager to persist and flush
        $entityManager->persist($utilisateur);
        $entityManager->flush();

        // Redirect or return a response
        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/update-password', name: 'update_password', methods: ['POST'])]
    public function updatePassword(Request $request, UtilisateurRepository $repository, SessionInterface $session): Response
    {
        $data = $request->request->all();
        $newPassword = $data['password'];
        $newPasswordConfirmation = $data['confirm_password'];
        $user = $session->get('user');

        if ($newPassword !== $newPasswordConfirmation) {
            return $this->redirectToRoute('forget_password_');
        }

        $repository->updatePasswor($user->getId(), $newPassword);
        return $this->redirectToRoute('app_login');
    }

    #[Route('/resive', name: 'resive', methods: ['POST'])]
    public function resivecode(Request $request, UtilisateurRepository $repository, SessionInterface $session): Response
    {
        $data = $request->request->all();
        $code = $data['code'];
        $codeSession = $session->get('code');


        if (($code == $codeSession) == true) {
            return $this->render('utilisateur/newPassword.html.twig');
        } else {
            return $this->redirectToRoute('forget_password_');
        }
    }


    #[Route('/ForgetPassword', name: 'ForgetPassword', methods: ['POST'])]
    public function ForgetPassword(Request $request, EntityManagerInterface $entityManager, UtilisateurRepository $repository, SessionInterface $session): Response
    {

        $data = $request->request->all();
        $email = $data['email'] ?? null;

        if (!$email) {
            // Assuming you have a flash message system or similar to notify the user
            $this->addFlash('error', 'No email provided.');
            return $this->redirectToRoute('your_fallback_route_here');
        }
        $utilisateur = new Utilisateur();
        $utilisateur = $repository->ForgetPassword($email);

        if ($utilisateur == null) {
            $this->addFlash('error', 'Email does not exist.');
            return $this->render('utilisateur/forgetPassword.html.twig');
        }

        $randomNumber = rand(1000, 9999);
        $session->set('code', $randomNumber);
        $session->set('user', $utilisateur);



        $message = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification de mot de passe</title>
    <style>
        body {
            font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo {
            width: 100px;
            display: block;
            margin: 0 auto 20px;
        }
        .content {
            text-align: center;
            margin-top: 20px;
        }
        .button {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .footer {
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="cid:logo" alt="Logo de votre société" class="logo">
        <div class="content">
            <p>Bonjour <strong>' . $utilisateur->getNomUtilisateur() . '</strong>,</p>
            <p>Vous avez demandé à réinitialiser votre mot de passe. Veuillez utiliser le code suivant pour procéder à la modification :</p>
            <p>Votre code de réinitialisation est : <strong>' . $randomNumber . '</strong></p>
            
           <p>Merci de faire confiance à notre service.</p>
        </div>
        <div class="footer">
            Pour toute assistance, contactez-nous au : +21623456789<br>
            <small>© Nom de votre société - Tous droits réservés</small>
        </div>
    </div>
</body>
</html>
';



        $emailSender = new EmailSender();
        $emailSender->sendEmail("saker.hajji13@gmail.com", "[Reset Password]", $message);

        $this->addFlash('success', 'A reset code has been sent to your email.');
        return $this->render('utilisateur/resivecode.html.twig');
    }

    #[Route('/', name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository,PartenairesRepository $partenairesRepository,LivraisonRepository $livraisonRepository , ProduitsRepository $produitsRepository ,LocationRepository  $locationRepository , EncheresRepository $encheresRepository ): Response
    {

        //partenaire
        $partenaires = $partenairesRepository->findAll();
        $dataP = [];
        foreach ($partenaires as $partenaire) {
            $dataP[] = [
                'nom' => $partenaire->getNom(),
                'points' => $partenaire->getPoints(),
            ];
        }
        //livraison
        $livraisonsStats = $livraisonRepository->countDeliveriesByStatus();

        $data = $this->session->get('user');
       $r=$data->getRole() ;


        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
            'user' => $data,
            'partenairesJson' => json_encode($dataP),
            'livraisonsStats' => $livraisonsStats,
            'nbproduit'=>$produitsRepository->countAllProducts() ,
            'nblocation'=>$locationRepository->countAllProducts(),
            'nbuser'=>$utilisateurRepository->countAllUsers() ,
            'nbencher'=>$encheresRepository->countAllenchers() ,
            'r'=>$r,
        ]);
    }

    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }





    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager ,UtilisateurRepository $repository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->request->get('_token'))) {
            $repository->deleteUserAndRelatedData($utilisateur->getId());
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }
}
