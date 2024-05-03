<?php

namespace App\Controller;
use App\Repository\ReservationLocationRepository;
use App\Repository\LocationRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\ReservationLocation;
use App\Entity\Utilisateur;
use App\Entity\Location;
use App\Repository\ImageRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\twilioo;
use Twilio\Rest\Client;





class ReservationController extends AbstractController
{

    /**
     * @Route("/reservation", name="app_reservation")
     */


     #[Route('/reservation', name: 'app_reservation')]
     public function index(Request $request, EntityManagerInterface $em,ImageRepository $imageRepository): Response
     {
         $locationId = $request->query->get('locationId'); // Get the location ID from the query parameters
         $loca = $em->getRepository(Location::class)->find($locationId);
         // Fetch the list of users
         $users = $this->getDoctrine()->getRepository(Utilisateur::class)->findAll();
     

          // Fetch images for the product
          $imagesByLocation = [];
          $imagesByLocation[$loca->getIdLocation()] = $imageRepository->findBy(['location' => $loca]);
  
  
         // Pass the location ID and the list of users to the reservation page
         return $this->render('reservation/reservation.html.twig', [
             'locationId' => $locationId,
             'users' => $users,
             'loca' => $loca,
             'imagesByLocation' => $imagesByLocation,
         ]);}


         #[Route('/reservation/submit', name: 'app_reservation_submit', methods: ['POST'])]
         public function submitReservation(Request $request, EntityManagerInterface $entityManager,ValidatorInterface $validator,): Response
         {
             // Get form data from the request
              // Get the location ID from the query parameters
     
             // Fetch the list of users
             $users = $this->getDoctrine()->getRepository(Utilisateur::class)->findAll();
             $locationId = $request->request->get('locationId');
             $userId = $request->request->get('user');
             $dateDebut = new \DateTime($request->request->get('date_debut'));
             $dateFin = new \DateTime($request->request->get('date_fin'));
             $note = $request->request->get('note');

             $entityManager = $this->getDoctrine()->getManager();

// Get the Location entity for the selected location ID
            $location = $entityManager->getRepository(Location::class)->find($locationId);
         
             // Get the User entity for the selected user ID
             $user = $entityManager->getRepository(Utilisateur::class)->find($userId);
             
         
             // Create a new ReservationLocation entity
             $reservation = new ReservationLocation();
             $reservation->setLocation2($location); // Assuming you have a setter method for locationId in ReservationLocation entity
             $reservation->setUser($user); // Set the user
             $reservation->setDateDebut($dateDebut);
             $reservation->setDateFin($dateFin);
             $reservation->setNotes($note);
              // Validate the entity
            $errors = $validator->validate($reservation);

            // If there are validation errors, render the form with the errors
            if (count($errors) > 0) {
               
                $locationId = $request->query->get('locationId');
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                
                // Pass error messages to the template
                return $this->render('reservation/reservation.html.twig', [
                    'errors' => $errorMessages,
                    'locationId' => $locationId,
                    'users' => $users,
                ]);
            }
        
         
             // Persist the reservation entity
             $entityManager->persist($reservation);
             $entityManager->flush();
         
             // Redirect the user to a success page or another page
             return $this->redirectToRoute('reservation');
         }

    // Delete function in your controller
    #[Route('/reservation/{id}', name: 'delete_reservation', methods: ['POST'])]
    public function deleteReservation(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reservationLocation = $entityManager->getRepository(ReservationLocation::class)->find($id);

        if (!$reservationLocation) {
            throw $this->createNotFoundException('Reservation not found.');
        }

        $entityManager->remove($reservationLocation);
        $entityManager->flush();

        // Redirect to a page or reload the current page after deletion
        return $this->redirectToRoute('reservation');
    }     

    #[Route('/reservation/{id}/update', name: 'update_reservation', methods: ['GET'])]
public function updateReservation(int $id): Response
{
    // Fetch the reservation from the database
    $reservationLocation = $this->getDoctrine()->getRepository(ReservationLocation::class)->find($id);

    if (!$reservationLocation) {
        throw $this->createNotFoundException('Reservation not found.');
    }
     // Fetch the list of users and locations from the database
     $users = $this->getDoctrine()->getRepository(Utilisateur::class)->findAll();
     $locations = $this->getDoctrine()->getRepository(Location::class)->findAll();

    // Render the update reservation form, passing the reservationLocation object to the template
    return $this->render('reservation/update.html.twig', [
        'reservationLocation' => $reservationLocation,
        'users' => $users,
        'locations' => $locations,
    ]);}

    #[Route('/reservation/update/{id}', name: 'submit_update_reservation', methods: ['POST'])]
public function submitUpdateReservation(Request $request, ReservationLocation $reservationLocation): Response
{
    // Retrieve data from the form submission
    $dateDebut = new \DateTime($request->request->get('date_debut'));
    $dateFin = new \DateTime($request->request->get('date_fin'));
    $userId = $request->request->get('user');
    
    
    $notes = $request->request->get('notes');
    
    // Get the User and Location entities from the provided IDs
    $entityManager = $this->getDoctrine()->getManager();
    $user = $entityManager->getRepository(Utilisateur::class)->find($userId);
   
    
    
    
    // Update the reservation location entity with the new data
    $reservationLocation->setDateDebut($dateDebut);
    $reservationLocation->setDateFin($dateFin);
    $reservationLocation->setUser($user);
    
    $reservationLocation->setNotes($notes);
    
    // Persist the changes to the database
    $entityManager->flush();
    
    // Redirect the user to a success page or another page
    return $this->redirectToRoute('reservation');
}



    #[Route('/reservationshow', name: 'reservation')]
    public function showReservations(): Response
    {
        $reservationLocations = $this->getDoctrine()->getRepository(ReservationLocation::class)->findAll();

        return $this->render('reservation/show.html.twig', [
            'reservationLocations' => $reservationLocations,
        ]);
    }

    
    #[Route('/reservationshowBack', name: 'reservationBack')]
    public function showReservationsBack(): Response
    {
        $reservationLocations = $this->getDoctrine()->getRepository(ReservationLocation::class)->findAll();

        return $this->render('reservation/reservationBack.html.twig', [
            'reservationLocations' => $reservationLocations,
        ]);
    }

    #[Route('/reservationClientshow', name: 'reservationClient')]
    public function showReservationClient( LocationRepository $lr, ReservationLocationRepository $reservationLocationRepository): Response
    {
        // Récupérer l'ID de l'utilisateur
        $userId = 2;
    
        // Fetch locations related to the user
        $availability = true; // Set the availability status you want to filter

        $locations = $lr->findByUserIdAndAvailability($userId, $availability);
    
    
        // Extract location IDs
        $locationIds = [];
        foreach ($locations as $location) {
            $locationIds[] = $location->getId();
        }
    
        // Fetch reservations for the user's locations
        $reservations = $reservationLocationRepository->findReservationsForLocations($locationIds);
    
        return $this->render('reservation/reservationClient.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/confirm-reservation/{id}', name: 'confirm_reservation')]
    public function confirmReservation(Request $request, int $id, twilioo $twilioService, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the reservation from the database based on the provided ID
    $reservation = $entityManager->getRepository(ReservationLocation::class)->find($id);

    // Ensure the reservation exists
    if (!$reservation) {
        throw $this->createNotFoundException('Reservation not found');
    }

    // Confirm the reservation (update the database status or perform any necessary logic)
    // For example, you might set a 'confirmed' flag in the Reservation entity

    // Mark the availability of the associated location as false
    $location = $reservation->getLocation();
    $location->setDisponibilite(false);

    // Persist the changes to the location entity
    $entityManager->persist($location);

    // Flush changes to the database
    $entityManager->flush();
        $userPhoneNumber = '+21654497997'; // Assuming you have a method to retrieve the user's phone number
        $message = 'Your reservation has been confirmed!';
        $twilioService->sendSms($userPhoneNumber, $message);

        // Redirect back to the page displaying reservations
        return $this->redirectToRoute('reservationClient');
    }
    

}