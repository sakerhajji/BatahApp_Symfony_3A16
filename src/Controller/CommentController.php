<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CommentRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\EventRepository;

use App\Entity\Comment;
use App\Form\CommentType;

use App\Repository\UserRepository;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\ImageRepository;
use App\Repository\ProduitsRepository;
use App\Repository\UtilisateurRepository;
use DateTime;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /******************************************************************************************************************************************* */
    /*********************************************************respensable********************************************************************************** */


    #[Route('/deletecommentA/{ref}/{idevent}', name: 'app_deletecommentA')]
    public function deleteCommentA($ref, $idProduit, CommentRepository $commentRepository, ProduitsRepository $produitRepository)
    {

        $comment = $commentRepository->find($ref);
        $produit = $produitRepository->find($idProduit);

        if (!$comment || !$produit) {
            throw $this->createNotFoundException('Comment or Event not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        //return $this->render('event/DetailA.html.twig', ['event' => $event]);
        return $this->redirectToRoute('app_ShoweventA', ['id' => $idProduit]);
    }




    /******************************************************************************************************************************************* */
    /*********************************************************client********************************************************************************** */


    #[Route('/Addcomment/{idp}/{iduser}', name: 'app_Addcomment_event')]
    public function AddCommentToEvent(int $idp, int $iduser, Request $request, ProduitsRepository $produitRepository, CommentRepository $commentRepository, UtilisateurRepository $userRepository, ImageRepository $imageRepository)
    {
        $product = $produitRepository->find($idp);
        $user = $userRepository->find($iduser);

        if (!$product || !$user) {
            throw $this->createNotFoundException('Product or User not found');
        }

        if ($request->isMethod('POST')) {
            $commentaire = $request->request->get('commentaire');

            $comment = new Comment();
            $comment->setIdProduit($product);
            $comment->setCommentaire($commentaire);
            $comment->setIdClient($user);
            // Ajoutez la date actuelle au commentaire
            $comment->setDate(new DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            // Rediriger vers l'interface de détails du produit avec l'ID du produit
            return $this->redirectToRoute('detailProduitFront', ['idp' => $idp]);
        }

        // Si la requête n'est pas de type POST, rend le template de détails du produit
        return $this->redirectToRoute('detailProduitFront', ['idp' => $idp]);
    }


    #[Route('/deletecomment/{ref}/{idp}/{iduser}', name: 'app_deletecomment')]
    public function deleteComment($ref, $idp, $iduser, CommentRepository $commentRepository, UtilisateurRepository $userRepository, ProduitsRepository $produitsRepository)
    {
        $comment = $commentRepository->find($ref);
        $user = $userRepository->find($iduser);
        $product = $produitsRepository->find($idp);

        if (!$comment || !$user || !$product) {
            throw $this->createNotFoundException('Comment, User, or prod not found');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('detailProduitFront', ['idp' => $idp, 'userId' => $iduser]);
    }




    /*********************************************************************************************************************************************** */
    /*********************************************************CRUD-COMMENT************************************************************************************** */

    #[Route('/editcomment/{id}', name: 'app_editcomment')]
    public function edit($id, CommentRepository $repository, Request $request)
    {
        $comment = $repository->find($id);

        // Vérifiez si le commentaire existe
        if (!$comment) {
            throw $this->createNotFoundException('Comment not found');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->add('Modifier', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, mettez à jour le commentaire
            $this->getDoctrine()->getManager()->flush();

            // Rediriger vers la page de détails du produit après modification du commentaire
            return $this->redirectToRoute('detailProduitFront', ['idp' => $comment->getIdProduit()->getIdProduit()]);
        }

        // Si la méthode n'est pas POST, rediriger vers la page de détails du produit
        return $this->redirectToRoute('detailProduitFront', ['idp' => $comment->getIdProduit()->getIdProduit()]);
    }



    #[Route('/Affichecomment', name: 'app_Affichecomment')]
    public function Affiche(CommentRepository $repository)
    {
        $comment = $repository->findAll(); //select *
        return $this->render('comment/Affiche.html.twig', ['comment' => $comment]);
    }


    #[Route('/Showcomment/{ref}', name: 'app_detailcomment')]
    public function showComment($ref, CommentRepository $repository)
    {
        $comment = $repository->find($ref);
        if (!$comment) {
            return $this->redirectToRoute('app_Affichecomment');
        }

        return $this->render('comment/show.html.twig', ['b' => $comment]);
    }





    #[Route('/Addcomment', name: 'app_Addcomment')]
    public function Add(CommentRepository $repository, Request $request)
    {
        $comment = new Comment();
        $form = $this->CreateForm(CommentType::class, $comment);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('app_Affichecomment');
        }
        return $this->render('Comment/Add.html.twig', ['f' => $form->createView()]);
    }
}
