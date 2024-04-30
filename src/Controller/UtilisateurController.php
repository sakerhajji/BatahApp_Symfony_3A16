<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use App\Service\EmailSender;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
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

    #[Route('/csv', name: 'app_csv_import', methods: ['POST'])]
    public function csvImport(Request $request): Response
    {
        // Retrieve the uploaded CSV file from the request
        $csvFile = $request->files->get('csvfile');

        // Check if a file was uploaded
        if (!$csvFile) {
            throw new \InvalidArgumentException('No CSV file uploaded.');
        }

        // Get the path to the temporary uploaded file
        $tmpFilePath = $csvFile->getPathname();

        // Read the CSV file into an array
        $csvData = array_map('str_getcsv', file($tmpFilePath));

        // Dump the CSV data for debugging
        dd($csvData);

        // Further processing of the CSV data (e.g., storing in database)

        // Return a response (if needed)
        return new Response('CSV file uploaded and processed successfully.');
    }
    private function readCsvFile(Request $request): array
    {
        // Get the uploaded CSV file from the request
        $csvFile = $request->files->get('csvFile');

        // Check if a file was uploaded
        if (!$csvFile instanceof UploadedFile) {
            throw new \RuntimeException('No file uploaded.');
        }

        // Check if the file is valid
        if (!$csvFile->isValid()) {
            throw new FileException('Invalid file uploaded.');
        }

        // Open the CSV file and read its content
        $fileContent = ByteString::fromPath($csvFile->getPathname())->toString();

        // Decode CSV content
        $csvEncoder = new CsvEncoder();
        $csvData = $csvEncoder->decode($fileContent, 'csv');

        return $csvData;
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

    #[Route('/profile', name: 'profile')]
    public function profile(UtilisateurRepository $repository)
    {
        $user = new Utilisateur() ;
        $user = $this->session->get("user");
        $user=$repository->find($user->getID()) ;
        return $this->render('utilisateur/profile.html.twig',
        ['user'=>$user]);
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

    #[Route('/', name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository  ): Response
    {
        $data= $this->session->get('user') ;

        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
            'user'=>$data ,
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
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }


}
