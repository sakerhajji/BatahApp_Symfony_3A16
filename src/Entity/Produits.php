<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitsRepository::class)]
class Produits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idProduit")]
    private int $idProduit;

    #[ORM\Column(name: "type", type: "string", length: 300, nullable: false)]
    private string $type;

    #[ORM\Column(name: "description", type: "string", length: 300, nullable: false)]
    private string $description;

    #[ORM\Column(name: "prix", type: "float", nullable: false)]
    private float $prix;

    #[ORM\Column(name: "labelle", type: "string", length: 300, nullable: false)]
    private string $labelle;

    #[ORM\Column(name: "status", type: "string", length: 255, nullable: false)]
    private string $status;

    #[ORM\Column(name: "periodeGarantie", type: "integer", nullable: false)]
    private int $periodeGarantie;

    #[ORM\Column(name: "photo", type: "string", length: 255, nullable: false)]
    private string $photo;

    #[ORM\Column(name: "localisation", type: "string", length: 255, nullable: false)]
    private string $localisation;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "idUtilisateur", referencedColumnName: "id")]
    private Utilisateur $idUtilisateur;

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getLabelle(): ?string
    {
        return $this->labelle;
    }

    public function setLabelle(string $labelle): self
    {
        $this->labelle = $labelle;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPeriodeGarantie(): ?int
    {
        return $this->periodeGarantie;
    }

    public function setPeriodeGarantie(int $periodeGarantie): self
    {
        $this->periodeGarantie = $periodeGarantie;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getIdUtilisateur(): ?Utilisateur
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?Utilisateur $idUtilisateur): self
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }
    public function __toString()
    {
        return $this->getIdProduit();
    }
}
