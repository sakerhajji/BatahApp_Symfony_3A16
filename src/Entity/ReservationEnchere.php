<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\ReservationEnchereRepository")]
class ReservationEnchere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idReservation", type: "integer")]
    private $idReservation;

    #[ORM\Column(name: "dateReservation", type: "date", nullable: true)]
    private $dateReservation;

    #[ORM\Column(name: "confirmation", type: "boolean", nullable: true)]
    private $confirmation;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "idUser", referencedColumnName: "id")]
    private $idUser;

    #[ORM\ManyToOne(targetEntity: Encheres::class)]
    #[ORM\JoinColumn(name: "idEnchere", referencedColumnName: "idEnchere")]
    private $idEnchere;

    public function getIdReservation(): ?int
    {
        return $this->idReservation;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(?\DateTimeInterface $dateReservation): self
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getConfirmation(): ?bool
    {
        return $this->confirmation;
    }

    public function setConfirmation(?bool $confirmation): self
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    public function getIdUser(): ?Utilisateur
    {
        return $this->idUser;
    }

    public function setIdUser(?Utilisateur $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdEnchere(): ?Encheres
    {
        return $this->idEnchere;
    }

    public function setIdEnchere(?Encheres $idEnchere): self
    {
        $this->idEnchere = $idEnchere;

        return $this;
    }
}
