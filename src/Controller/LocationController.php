<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Location;
use App\Entity\Image;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;


use Symfony\Component\HttpFoundation\File\Exception\FileException;



class LocationController extends AbstractController
{
    #[Route('/location', name: 'app_location')]
    public function index(): Response
    {
        return $this->render('location/index2.html.twig', [
            'controller_name' => 'LocationController',
        ]);
    }


    #[Route('/ha', name: 'app_afficahgel')]
    public function front(LocationRepository $lr): Response
    {
        return $this->render('location/page-shop.html.twig', ['locations' => $lr->findAll()]);
    }

    #[Route('/ajoutlocation', name: 'app_ajout_location')]
    public function addLocation(Request $request, ValidatorInterface $validator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission and validation

            $entityManager->persist($location);
            $entityManager->flush();

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                try {
                    $originalFilename = $imageFile->getClientOriginalName();
                    $extension = $imageFile->getClientOriginalExtension();
                    $newFilename = uniqid() . '.' . $extension;

                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFilename
                    );

                    $imagePath = '/uploads/' . $newFilename;

                    $image = new Image();
                    $image->setUrl($imagePath);
                    $image->setLocation($location);

                    $entityManager->persist($image);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload the image.');
                    return $this->redirectToRoute('app_ajout_location');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Location added successfully.');
            return $this->redirectToRoute('app_location_back_affiche');
        }

        return $this->render('location/page-dashboard-add-locations.html.twig', [
            'form' => $form->createView(),
        'errors' => $form->getErrors(true, false),
 
        ]);
    }
    
    #[Route('/modifier/{idl}', name: 'app_modifier_location')]
public function editLocation(Request $request, ValidatorInterface $validator, LocationRepository $lr, ImageRepository $imageRepository, $idl): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $location = $lr->find($idl);

    if (!$location) {
        throw $this->createNotFoundException('Location not found');
    }

    $form = $this->createForm(LocationType::class, $location);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        // Validate the form data
        $errors = $validator->validate($location);

        if (count($errors) === 0) {
            // If there are no validation errors, proceed with form submission

            // Traitement de l'image (Image processing)
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Code pour traiter l'upload de l'image (Code to process image upload)

                // Generate a unique filename using the original file extension
                $originalFilename = $imageFile->getClientOriginalName();
                $extension = $imageFile->getClientOriginalExtension();
                $newFilename = uniqid() . '.' . $extension;

                // Move the uploaded file to the desired location
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $newFilename
                );

                // Retrieve the existing image associated with the location
                $existingImage = $imageRepository->findOneBy(['location' => $location]);

                if ($existingImage) {
                    // Remove the existing image file from the file system
                    $existingImagePath = $existingImage->getUrl();
                    // Delete the image file if it exists
                    if (file_exists($existingImagePath)) {
                        unlink($existingImagePath);
                    }
                } else {
                    // Create a new image entity if no existing image found
                    $existingImage = new Image();
                    $existingImage->setLocation($location);
                    $entityManager->persist($existingImage);
                }

                // Update the image URL
                $existingImage->setUrl('/uploads/' . $newFilename);
            }

            // Save the changes to the database
            $entityManager->flush();

            $this->addFlash('success', 'Location updated successfully.');
            return $this->redirectToRoute('app_location_back_affiche');
        } else {
            // If there are validation errors, render the form with errors
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }
    }

    // Render the form with errors
    return $this->render('location/page-dashboard-edit-locations.html.twig', [
        'form' => $form->createView(),
    ]);


    return $this->render('location/page-dashboard-edit-locations.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/locationback', name: 'app_location_back_affiche')]
    public function showprodback(
        Request $request,
        ManagerRegistry $em,
        LocationRepository $lr,
        ImageRepository $imageRepository,
        PaginatorInterface $paginator
    ): Response {
        $typeFilter = $request->query->get('type');
        $prixMax = $request->query->get('prix');

        // Fetch locations
        if ($typeFilter || $prixMax) {
            $criteria = [];
            if ($typeFilter) {
                $criteria['type'] = $typeFilter;
            }
            if ($prixMax) {
                $criteria['prix'] = $prixMax;
            }
            $locations = $lr->findBy($criteria);
        } else {
            $locations = $lr->findAllWithUser(); // Assuming you have a custom method findAllWithUser in LocationRepository to join User entity
        }

        // Paginate the results
        $pagination = $paginator->paginate(
            $locations, // Query results
            $request->query->getInt('page', 1), // Page number
            10 // Limit per page
        );

        // Fetch images
        $imagesByLocation = [];
        foreach ($pagination as $location) {
            $imagesByLocation[$location->getIdLocation()] = $imageRepository->findBy(['location' => $location]);
        }

        return $this->render('location/page-dashboard-listing.html.twig', [
            'pagination' => $pagination,
            'imagesByLocation' => $imagesByLocation,
        ]);
    }

    #[Route('/supprimer/{idl}', name: 'app_supprimer_location')]
    public function removeloca(ManagerRegistry $em, Request $request, LocationRepository $lr, ImageRepository $imageRepository, $idl): Response
    {

        $loca = $lr->find($idl);
        if (!$loca) {
            // Handle not found scenario, for example:
            throw $this->createNotFoundException('Location not found');
        }
        // Find images associated with the location
        $images = $imageRepository->findByLocation($loca);

        // Delete each associated image

        // Optionally, delete the image from the database
        $em = $em->getManager();
        $em->remove($images);


        $em->remove($loca);
        $em->flush();
        return $this->redirectToRoute('app_location_back_affiche');
    }

    #[Route('/details/{idl}', name: 'app_details')]
    public function details(LocationRepository $lr, $idl): Response
    {
        $location = $lr->find($idl);


        return $this->render('location/page-car-single-v1.html.twig', [
            'location' => $location,

        ]);
    }
    #[Route('/detail', name: 'app_detail')]
    public function detail(): Response
    {
        return $this->render('produit/page-car-single-v1.html.twig');
    }

   
}
