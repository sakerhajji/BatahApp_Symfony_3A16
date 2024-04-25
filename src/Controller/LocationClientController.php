<?php

namespace App\Controller;
use App\Entity\Utilisateur;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Location;
use App\Entity\Image;
use App\Form\LocationType;

use App\Repository\LocationRepository;
use App\Entity\ReservationLocation;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class LocationClientController extends AbstractController
{
    #[Route('/location/client', name: 'app_location_client')]
    public function index(): Response
    {
        return $this->render('location/affichageLocation.html.twig', [
            'controller_name' => 'LocationClientController',
        ]);
    }

    #[Route('/locationfront', name: 'app_location_front_affiche')]
    public function showprodback(ManagerRegistry $em, LocationRepository $lr, ImageRepository $imageRepository): Response
    {
        // Fetch locations
        $locations = $lr->findAllWithUser(); // Assuming you have a custom method findAllWithUser in LocationRepository to join User entity
    
        // Fetch images
        $imagesByLocation = [];
        foreach ($locations as $location) {
            $imagesByLocation[$location->getIdLocation()] = $imageRepository->findBy(['location' => $location]);
        }
    
        return $this->render('location_client/affichageLocation.html.twig', [
            'locations' => $locations,
            'imagesByLocation' => $imagesByLocation,
        ]);
    }
  

    #[Route('/locationfrontAdd', name: 'app_location_front_add')]
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
                    return $this->redirectToRoute('app_location_front_add');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Location added successfully.');
            return $this->redirectToRoute('app_location_client_affiche');
        }

        return $this->render('location_client/page-dashboard-add-front.html.twig', [
            'form' => $form->createView(),
        'errors' => $form->getErrors(true, false),
 
        ]);
    }


    #[Route('/locationclient', name: 'app_location_client_affiche')]
    public function locationClient(ManagerRegistry $em, LocationRepository $lr, ImageRepository $imageRepository): Response
    {
    $userId = 2; // Change this to the desired user id
    $locations = $lr->findByUserId($userId);

    // Fetch images
    $imagesByLocation = [];
    foreach ($locations as $location) {
        $imagesByLocation[$location->getIdLocation()] = $imageRepository->findBy(['location' => $location]);
    }

    return $this->render('location_client/affichageClient.html.twig', [
        'locations' => $locations,
        'imagesByLocation' => $imagesByLocation,
    ]);
    
     
}
#[Route('/statistiques', name: 'app_location_client_statistiques')]
public function statistiques(LocationRepository $locationRepository): Response
{
    // Utilize the repository function to count reservations by location
    $reservationsByLocation = $locationRepository->countReservationsByLocation();

    // Format the data for the first chart
    $formattedData = [];
    foreach ($reservationsByLocation as $row) {
        // Retrieve the description of the location from the associative array returned by the SQL query
        $location = $locationRepository->find($row['location_id']);
        $description = $location ? $location->getDescription() : 'Description introuvable';
        
        $formattedData[$description] = $row['reservation_count'];
    }

    // Utilize the repository function to count locations by type
    $carLocations = $locationRepository->countLocationsByType('voiture');
    $houseLocations = $locationRepository->countLocationsByType('maison');

    // Pass the counts to the view
    return $this->render('location_client/statistiques.html.twig', [
        'reservationsByLocation' => $formattedData,
        'carLocationsCount' => $carLocations['location_count'],
        'houseLocationsCount' => $houseLocations['location_count'],
    ]);
}



#[Route('/reservationClient', name: 'reservation_client')]

public function showReservations(LocationRepository $locationRepository): Response
{
    $userId = 2; // ID de l'utilisateur désiré

    // Récupérer les locations publiées par l'utilisateur spécifié
    $locations = $locationRepository->findByUserId($userId);

    // Créer un tableau pour stocker les réservations associées aux locations de l'utilisateur
    $reservations = [];

    // Pour chaque location de l'utilisateur
    foreach ($locations as $location) {
        // Récupérer les réservations associées à cette location
        $locationReservations = $location->getReservations();

        // Ajouter chaque réservation à la liste des réservations
        foreach ($locationReservations as $reservation) {
            $reservations[] = $reservation;
        }
    }

    // Passer les réservations au modèle Twig pour l'affichage
    return $this->render('location_client/affichageReservationClient.html.twig', [
        'reservations' => $reservations,
    ]);
}


}
