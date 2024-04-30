<?php
// src/Controller/CsvController.php
// src/Controller/CsvController.php
namespace App\Controller;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CsvController extends AbstractController
{


    #[Route('/upload-csv', name: 'upload_csv', methods: ['POST'])]
    public function uploadCsv(Request $request): Response
    {
        $file = $request->files->get('csv_file');
        if ($file && $file->isValid()) {
            $csvData = $this->parseCsvFile($file);
            return $this->render('utilisateur/_csv_data.html.twig', [
                'data' => $csvData,
            ]);
        }

        return new Response('No file or invalid file provided.', 400);
    }

    private function parseCsvFile($file): array
    {
        $data = [];
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Check if the row has at least 9 columns
                if (count($row) >= 9) {
                    $data[] = [
                        'firstname' => trim($row[0]),
                        'lastname' => trim($row[1]),
                        'email' => trim($row[2]),
                        'datedenaissance' => trim($row[3]),
                        'sexe' => trim($row[4]),
                        'telephone' => trim($row[5]),
                        'cin' => trim($row[6]),
                        'password' => trim($row[7]),
                        'role' => trim($row[8])
                    ];
                } else {
                    // Optionally log or handle rows with insufficient columns
                    error_log('Skipped a row with insufficient data: ' . json_encode($row));
                }
            }
            fclose($handle);
        }
        return $data;
    }

    #[Route('/handle-data', name: 'handle-data', methods: ['POST'])]
    public function handleData(Request $request, EntityManagerInterface $entityManager , UtilisateurRepository $repository): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'status' => 'error',
                'message' => 'No data provided!'
            ], Response::HTTP_BAD_REQUEST);
        }
 $nb=1 ;
        foreach ($data as $index => $row) {
                $dateOfBirth = \DateTime::createFromFormat('Y-m-d', $row['datedenaissance']);
                $user = new Utilisateur();
                $user->setPrenomutilisateur($row['firstname']);
                $user->setNomutilisateur($row['lastname']);
                $user->setAdresseemail($row['email']);
                $user->setDatedenaissance($dateOfBirth);
                $user->setSexe($row['sexe']);
                $user->setNumerotelephone($row['telephone']);
                $user->setNumerocin($row['cin']);
                $user->setMotdepasse(password_hash($row['password'], PASSWORD_BCRYPT));
                $user->setRole($row['role']);
                $check= new InputControl() ;
            $checkMail=$repository->findBy(['adresseemail'=>$user->getAdresseemail()]) ;
            if ($check->checkPasswordStrength($user->getMotdepasse()) &&
                $check->verifierNom($user->getNomutilisateur()) &&
                $check->verifierNom($user->getPrenomutilisateur()) &&
                $check->verifyEmail($user->getAdresseemail())&&
                $user->getRole()=="A" || $user->getRole()=="U"&&
                $user->getSexe()=="H"||$user->getRole()=="F"&&
                empty($checkMail)

            ){
                $entityManager->persist($user);
                $nb +=1 ;
            }
            else
            {
                return $this->json([
                    'status' => 'error',
                    'message' => 'verifier les informations de votre fichier possibilite de mail existe ou ilya un champe respect pas les controle de sasire  ligne numero = '.$nb
                ]);
            }

            }
                $entityManager->flush(); // Commit all changes


        return $this->json([
            'status' => 'done',
            'message' => 'All data processed successfully!'
        ]);
    }






}


