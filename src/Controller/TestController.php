<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test') ]
    public function index(SessionInterface $session, MailerInterface $mailer , UtilisateurRepository $repository ): Response
    {
//        $emailSender = new EmailSender();
//        $emailSender->sendEmail();
dd($session->get('user'))  ;
        return $this->render('utilisateur/profile.html.twig');

    }
    #[Route('/MisAjour', name: 'MisAjour', methods: ['POST'])]

    public function MisAjour(Request $request, EntityManagerInterface $entityManager, SessionInterface $session , PictureService $pictureService ): Response
    {
        $imageFile = $request->files->get('image');
        $user = $session->get('user');
        $data = $request->request->all();
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = uniqid().'.'.$imageFile->guessExtension();
        try {
            $imageFile->move(
                $this->getParameter('images_directory'),
                $newFilename
            );
            $user->setAvatar($newFilename) ;
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        // Check if user is in session



        // Update user properties
        if (isset($data['form_name'])) {
            $user->setNomutilisateur($data['form_name']);
        }
        if (isset($data['form_prenom'])) {
            $user->setPrenomutilisateur($data['form_prenom']);
        }
        if (isset($data['form_tlf'])) {
            $user->setNumerotelephone($data['form_tlf']);
        }
        if (isset($data['pays'])) {
            $user->setPays($data['pays']);
        }
        if (isset($data['form_Cin'])) {
            $user->setNumerocin($data['form_Cin']);
        }
        if (isset($data['form_date'])) {
            $datedenaissance = \DateTime::createFromFormat('Y-m-d', $data['form_date']);
            if ($datedenaissance instanceof \DateTime) {
                $user->setDatedenaissance($datedenaissance);
            }

        }
        $qb = $entityManager->createQueryBuilder();

        // Create the query
        $query = $qb->update(Utilisateur::class, 'u')
            ->set('u.nomutilisateur', ':nom')
            ->set('u.prenomutilisateur', ':prenom')
            ->set('u.numerotelephone', ':telephone')
            ->set('u.pays', ':pays')
            ->set('u.numerocin', ':cin')
            ->set('u.datedenaissance', ':date')
            ->set('u.avatar',':avatar')
            ->setParameter('nom', $data['form_name'])
            ->setParameter('prenom', $data['form_prenom'])
            ->setParameter('telephone', $data['form_tlf'])
            ->setParameter('pays', $data['pays'])
            ->setParameter('cin', $data['form_Cin'])
            ->setParameter('avatar',$newFilename)
            ->setParameter('date', \DateTime::createFromFormat('Y-m-d', $data['form_date']))
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery();

        // Execute the query
        $query->execute();

        // Redirect or return a response
        return $this->redirectToRoute('profile');
    }



    #[Route('/search', name: 'search_users')]
    public function search(Request $request): Response
    {
        // Retrieve the search query from the request
        $query = $request->query->get('query');

        // Check if the search query is null or empty
        if ($query === null || trim($query) === '') {
            // Return a JSON response with an error message indicating invalid search query
            return $this->json(['error' => 'Invalid search query']);
        }

        // Query the database for users matching the search query
        $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
        $utilisateurs = $repository->createQueryBuilder('u')
            ->where('u.nomutilisateur LIKE :query')
            ->andWhere('u.nomutilisateur IS NOT NULL')
            ->orWhere('u.prenomutilisateur LIKE :query')
            ->andWhere('u.prenomutilisateur IS NOT NULL')
            ->orWhere('u.adresseemail LIKE :query')
            ->andWhere('u.adresseemail IS NOT NULL')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();

        // Serialize the user data to an array
        $jsonData = [];
        foreach ($utilisateurs as $utilisateur) {
            $jsonData[] = [
                'id' => $utilisateur->getId(),
                'nomutilisateur' => $utilisateur->getNomutilisateur(),
                'prenomutilisateur' => $utilisateur->getPrenomutilisateur(),
                'adresseemail' => $utilisateur->getAdresseemail(),
                // Add more fields if needed
            ];
        }

        // Return a JSON response with the user data
        return $this->json($jsonData);
    }


}
