<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\CommentRepository;

class CommentaireController extends AbstractController
{
    /**
     * @Route("/addComment", name="add_comment", methods={"POST"})
     */
    public function addComment(Request $request): JsonResponse
    {
        // Récupérer les données du formulaire
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données nécessaires sont présentes
        if (!isset($data['commentaire'])) {
            return new JsonResponse(['message' => 'Le champ commentaire est requis'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Créer une nouvelle instance de Comment
        $comment = new Comment();
        $comment->setCommentaire($data['commentaire']);
        $comment->setDate(new \DateTime());
        $comment->setIdClient($this->getUser());

        // Enregistrer le commentaire dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        // Retourner une réponse JSON
        return new JsonResponse(['message' => 'Commentaire ajouté avec succès'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/deleteComment/{id}", name="delete_comment", methods={"DELETE"})
     */
    public function deleteComment(Comment $comment): JsonResponse
    {
        // Vérifier si l'utilisateur a le droit de supprimer ce commentaire
        if ($this->getUser() !== $comment->getIdClient()) {
            return new JsonResponse(['message' => 'Vous n\'avez pas la permission de supprimer ce commentaire'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Supprimer le commentaire
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        // Retourner une réponse JSON
        return new JsonResponse(['message' => 'Commentaire supprimé avec succès'], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/updateComment/{id}", name="update_comment", methods={"PUT"})
     */
    public function updateComment(Request $request, Comment $comment): JsonResponse
    {
        // Récupérer les données du formulaire
        $data = json_decode($request->getContent(), true);

        // Mettre à jour le commentaire
        $comment->setCommentaire($data['commentaire']);

        // Enregistrer les modifications dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        // Retourner une réponse JSON
        return new JsonResponse(['message' => 'Commentaire mis à jour avec succès'], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/comments", name="get_comments", methods={"GET"})
     */
    public function getComments(CommentRepository $commentRepository): JsonResponse
    {
        $comments = $commentRepository->findAll();

        return $this->json($comments, 200, [], ['groups' => 'comment']);
    }
}
