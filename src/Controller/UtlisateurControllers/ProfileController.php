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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class ProfileController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    #[Route('/profile', name: 'profile')]
    public function index(UtilisateurRepository $repository, request $request): Response
    {
        $user = new Utilisateur();
        $user = $this->session->get("user");
        $user = $repository->find($user->getID());
        $errorMsg = $request->get('errorMsg');

        return $this->render(
            'profile/profile.html.twig',
            ['user' => $user,
                'errorMsg' => $errorMsg
            ]);
    }

    #[Route('/changepass', name: 'changepass', methods: ['POST'])]
    public function changepass(Request $request, UtilisateurRepository $repository, SessionInterface $session): Response
    {
        $data = $request->request->all();
        $user = $session->get('User');
        $oldpass = $data['oldPass'];
        $newPassword = $data['newPass'];
        $cnewPassword = $data['cnewPass'];
        $user = $session->get('user');
        $errorMsg=null;

        if (password_verify($oldpass, $user->getMotdepasse())) {

            if ($newPassword !== $cnewPassword) {
            $errorMsg = 'comfier votre mots de pass svp ';
                return $this->redirectToRoute('profile', [
                    'errorMsg' => $errorMsg,

                ], Response::HTTP_SEE_OTHER);

        }
        $affectedRows = $repository->updatePasswor($user->getId(), $newPassword);

        }
        return $this->redirectToRoute('app_login');

    }

    #[Route('/MisAjour', name: 'MisAjour', methods: ['POST'])]

    public function MisAjour(Request $request, EntityManagerInterface $entityManager, SessionInterface $session , PictureService $pictureService ): Response
    {

        $user = $session->get('user');
        $data = $request->request->all();
        $imageFile = $request->files->get('image');
        if($imageFile!=null) {
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
        }
        else
        {
            $newFilename=$user->getAvatar() ;
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

}
