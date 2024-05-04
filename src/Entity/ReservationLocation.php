<?php
namespace App\Entity;

use App\Repository\ReservationLocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationLocationRepository::class)]
#[ORM\Table(name: "reservation_location")]
class ReservationLocation

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_reservation_location = null;

    #[ORM\Column(name: "dateDebut", type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: 'La date de début ne peut pas être vide.')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: "dateFin", type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: 'La date de fin ne peut pas être vide.')]
    private ?\DateTimeInterface $dateFin = null;


    
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "idUtilisateur", referencedColumnName: "id")]
    private ?Utilisateur $user = null;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: "reservations")]
    #[ORM\JoinColumn(name: "idLocation", referencedColumnName: "idLocation",)]
    private ?location $location;

    #[ORM\Column(name: "notes",length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'La note ne peut pas être vide.')]
    private ?string $notes = null;

    public function getId(): ?int
    {
        return $this->id_reservation_location ;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }


    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;
        return $this;
    }
  
    public function getUser(): ?Utilisateur
    {
        return $this->user;}


    public function setLocation2(?Location $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function setUser(?Utilisateur $user): static
    {
        $this->user = $user;
        return $this;
    }    
        
        

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }
}
