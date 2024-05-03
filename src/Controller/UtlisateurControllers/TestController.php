<?php

namespace App\Controller\UtlisateurControllers;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\PictureService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function index(SessionInterface $session, MailerInterface $mailer, UtilisateurRepository $repository): Response
    {
//        $emailSender = new EmailSender();
//        $emailSender->sendEmail();


        $data = $session->get('user');
        return $this->render('utilisateur/csv_upload.html.twig', [
            'user' => $data,
        ]);

    }

    #[Route('/MisAjour', name: 'MisAjour', methods: ['POST'])]
    public function MisAjour(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, PictureService $pictureService): Response
    {

        $user = $session->get('user');
        $data = $request->request->all();
        $imageFile = $request->files->get('image');
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = uniqid() . '.' . $imageFile->guessExtension();
        try {
            $imageFile->move(
                $this->getParameter('images_directory'),
                $newFilename
            );
            $user->setAvatar($newFilename);
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
            $datedenaissance = DateTime::createFromFormat('Y-m-d', $data['form_date']);
            if ($datedenaissance instanceof DateTime) {
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
            ->set('u.avatar', ':avatar')
            ->setParameter('nom', $data['form_name'])
            ->setParameter('prenom', $data['form_prenom'])
            ->setParameter('telephone', $data['form_tlf'])
            ->setParameter('pays', $data['pays'])
            ->setParameter('cin', $data['form_Cin'])
            ->setParameter('avatar', $newFilename)
            ->setParameter('date', DateTime::createFromFormat('Y-m-d', $data['form_date']))
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery();

        // Execute the query
        $query->execute();

        // Redirect or return a response
        return $this->redirectToRoute('profile');
    }


}
