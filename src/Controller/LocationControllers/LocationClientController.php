<?php

namespace App\Controller\LocationControllers;

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
use Proxies\__CG__\App\Entity\Location as EntityLocation;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LocationClientController extends AbstractController
{

    private $managerRegistry;
    private $session;

    public function __construct(ManagerRegistry $managerRegistry, SessionInterface $session)
    {
        $this->managerRegistry = $managerRegistry;
        $this->session = $session;
    }


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
            return $this->redirectToRoute('app_location_client_affiche');
        }

        return $this->render('location_client/page-dashboard-add-front.html.twig', [
            'form' => $form->createView(),
            'errors' => $form->getErrors(true, false),

        ]);
    }
    #[Route('/locationclient', name: 'app_location_client_affiche')]
    public function locationClient(Request $request, SessionInterface $session, ManagerRegistry $em, LocationRepository $lr, ImageRepository $imageRepository): Response
    {
        $session = $request->getSession();
        $connectedUser = $session->get('user');
        $userId = $connectedUser->getId(); // Supposons que getId() renvoie l'identifiant de l'utilisateur

        $availability = true; // Set the availability status you want to filter

        $locations = $lr->findByUserIdAndAvailability($userId, $availability);

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

    #[Route('/likeReclamationsLocation/{idLocation}', name: 'likeReclamationsLocation', methods: ['POST'])]
    public function likeReclamations(Request $request, $idLocation): JsonResponse
    {
        $reponse = $this->managerRegistry->getRepository(Location::class)->find($idLocation);

        if (!$reponse) {
            throw $this->createNotFoundException('Réponse non trouvée');
        }

        $likesCount = $reponse->getLikes();
        $reponse->setLikes($likesCount == 0 ? 1 : 0);

        $em = $this->managerRegistry->getManager();
        $em->persist($reponse);
        $em->flush();

        return new JsonResponse(['likesCount' => $reponse->getLikes()]);
    }
}
