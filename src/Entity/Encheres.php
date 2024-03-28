<?php

namespace App\Entity;

use App\Repository\EncheresRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EncheresRepository::class)]
class Encheres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idEnchere", type: "integer")]
    private int $idEnchere;

    #[ORM\Column(name: "dateDebut", type: "date", nullable: true)]
    private ?\DateTimeInterface $dateDebut;

    #[ORM\Column(name: "dateFin", type: "date", nullable: true)]
    private ?\DateTimeInterface $dateFin;

    #[ORM\Column(name: "Status", type: "boolean", nullable: true)]
    private ?bool $status;

    #[ORM\Column(name: "prixMin", type: "float", precision: 10, scale: 2, nullable: true)]
    private ?float $prixMin;

    #[ORM\Column(name: "prixMax", type: "float", precision: 10, scale: 2, nullable: true)]
    private ?float $prixMax;

    #[ORM\Column(name: "prixActuelle", type: "float", precision: 10, scale: 2, nullable: true)]
    private ?float $prixActuelle;

    #[ORM\Column(name: "nbrParticipants", type: "integer", nullable: false)]
    private int $nbrParticipants;

    #[ORM\ManyToOne(targetEntity: Produits::class)]
    #[ORM\JoinColumn(name: "idProduit", referencedColumnName: "idProduit")]
    private ?Produits $produit;

    public function getIdEnchere(): ?int
    {
        return $this->idEnchere;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): void
    {
        $this->status = $status;
    }

    public function getPrixMin(): ?float
    {
        return $this->prixMin;
    }

    public function setPrixMin(?float $prixMin): void
    {
        $this->prixMin = $prixMin;
    }

    public function getPrixMax(): ?float
    {
        return $this->prixMax;
    }

    public function setPrixMax(?float $prixMax): void
    {
        $this->prixMax = $prixMax;
    }

    public function getPrixActuelle(): ?float
    {
        return $this->prixActuelle;
    }

    public function setPrixActuelle(?float $prixActuelle): void
    {
        $this->prixActuelle = $prixActuelle;
    }

    public function getNbrParticipants(): ?int
    {
        return $this->nbrParticipants;
    }

    public function setNbrParticipants(int $nbrParticipants): void
    {
        $this->nbrParticipants = $nbrParticipants;
    }

    public function getProduit(): ?Produits
    {
        return $this->produit;
    }

    public function setProduit(?Produits $produit): void
    {
        $this->produit = $produit;
    }
}
