<?php

namespace App\Controller\LocationControllers;

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
        $connectedUser = $request->getSession()->get('user');


        $entityManager = $this->getDoctrine()->getManager();
        $location = new Location();
        $location->setId($connectedUser);
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission and validation

            $entityManager->persist($location);
            $entityManager->flush();


            $imageFiles = $form->get('images')->getData(); // Utilisez le nom de champ associé à la relation OneToMany

            foreach ($imageFiles as $imageFile) {
                if ($imageFile) {
                    try {
                        // Générez un nom de fichier unique en utilisant l'extension de fichier d'origine
                        $originalFilename = $imageFile->getClientOriginalName();
                        $extension = $imageFile->getClientOriginalExtension();
                        $newFilename = uniqid() . '.' . $extension;

                        $imageFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFilename
                        );

                        // Définissez le chemin réel de l'image téléchargée dans l'entité Image
                        $imagePath = '/uploads/' . $newFilename; // Chemin relatif depuis le répertoire public

                        // Créez une nouvelle entité Image
                        $image = new Image();
                        $image->setUrl($imagePath);
                        // Associez l'image au produit
                        $location->addImage($image);

                        // Persistez l'entité Image
                        $entityManager->persist($image);
                    } catch (FileException $e) {
                        // Gérer l'erreur de téléchargement de fichier
                        $this->addFlash('error', 'Failed to upload one or more images.');
                        return $this->redirectToRoute('app_ajout_location');
                    }
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Location added successfully.');
            return $this->redirectToRoute('app_location_back_affiche');
        }

        return $this->render('location/page-dashboard-add-locations.html.twig', [
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, false),
            'connectedUser' => $connectedUser,

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

        $errors = []; // Initialize $errors variable

        if ($form->isSubmitted()) {
            // Validate the form data
            $errors = $validator->validate($location);

            // Handle image upload if form is submitted
            $existingImages = $imageRepository->findBy(['location' => $location]);
            $imageFiles = $form->get('images')->getData(); // Récupère un tableau d'objets UploadedFile

            // Supprimer les images existantes associées au produit
            foreach ($existingImages as $existingImage) {
                $existingImagePath = $existingImage->getUrl();
                if (file_exists($existingImagePath)) {
                    unlink($existingImagePath);
                }
                // Supprimer l'image de la base de données
                $entityManager->remove($existingImage);
            }

            // Enregistrer les nouvelles images associées au produit
            foreach ($imageFiles as $imageFile) {
                if ($imageFile) {
                    try {
                        // Générer un nom de fichier unique en utilisant l'extension de fichier d'origine
                        $originalFilename = $imageFile->getClientOriginalName();
                        $extension = $imageFile->getClientOriginalExtension();
                        $newFilename = uniqid() . '.' . $extension;

                        // Déplacer le fichier téléchargé vers l'emplacement désiré
                        $imageFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFilename
                        );

                        // Créer une nouvelle entité Image
                        $newImage = new Image();
                        $newImage->setUrl('/uploads/' . $newFilename);
                        $newImage->setLocation($location);

                        // Persistez l'entité Image
                        $entityManager->persist($newImage);
                    } catch (FileException $e) {
                        // Gérer l'erreur de téléchargement de fichier
                        $this->addFlash('error', 'Failed to upload one or more images.');
                        return $this->redirectToRoute('app_ajout_location');
                    }
                }
            }

            // Save the changes to the database
            $entityManager->flush();

            $this->addFlash('success', 'Location updated successfully.');
            return $this->redirectToRoute('app_location_back_affiche');
        }

        return $this->render('location/page-dashboard-edit-locations.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors, // Pass errors to the template
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
            5 // Limit per page
        );

        $currentPage = $request->query->getInt('page', 1);
        $itemsPerPage = 5;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $searchQuery = $request->query->get('search', '');
        if ($searchQuery !== '') {
            $products = $lr->findBySearchQuery($searchQuery, $itemsPerPage, $offset);
            $totalItems = count($products);
        } else {
            $products = $lr->findBy([], null, $itemsPerPage, $offset);
            $totalItems = $lr->count([]);
        }
        $totalPages = ceil($totalItems / $itemsPerPage);

        // Fetch images
        $imagesByLocation = [];
        foreach ($pagination as $location) {
            $imagesByLocation[$location->getIdLocation()] = $imageRepository->findBy(['location' => $location]);
        }

        return $this->render('location/page-dashboard-listing.html.twig', [
            'pagination' => $pagination,
            'imagesByLocation' => $imagesByLocation,
            'searchQuery' => $searchQuery,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
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
