<?php

namespace App\Controller\ProduitControllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Image;
use App\Entity\Location;

class ImageController extends AbstractController
{
    #[Route('/image', name: 'app_image')]
    public function index(): Response
    {
        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }
    #[Route('/upload-image/{locationId}', name: 'upload_image', methods: ['POST'])]
    public function uploadImage(Request $request, int $locationId): Response
    {
        // Handle image upload
        $uploadedFile = $request->files->get('image');

        if (!$uploadedFile) {
            // Handle case when no image is uploaded
            return new Response('No image uploaded', Response::HTTP_BAD_REQUEST);
        }

        // Move the uploaded file to the uploads directory
        $destination = $this->getParameter('uploads_directory');
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        try {
            $uploadedFile->move(
                $destination,
                $newFilename
            );
        } catch (FileException $e) {
            // Handle file upload error
            return new Response('Error uploading file', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Create and persist the Image entity
        $entityManager = $this->getDoctrine()->getManager();
        $image = new Image();
        $image->setUrl($newFilename); // Assuming you have a 'filename' property in your Image entity

        // Find the associated Location entity
        $location = $entityManager->getRepository(Location::class)->find($locationId);
        if (!$location) {
            return new Response('Location not found', Response::HTTP_NOT_FOUND);
        }

        // Associate the image with the location
        $image->setLocation($location);

        // Persist the Image entity
        $entityManager->persist($image);
        $entityManager->flush();

        return new Response('Image uploaded successfully', Response::HTTP_CREATED);
    }
}
