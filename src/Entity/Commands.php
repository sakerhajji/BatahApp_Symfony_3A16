<?php

namespace App\Entity;

use App\Repository\CommandsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandsRepository::class)]
class Commands
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $modeLivraison = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $modePaiement = null;

    #[ORM\Column(type: Types::FLOAT, precision: 10, scale: 0, nullable: true)]
    private ?float $coutTotale = null;

    #[ORM\Column(length: 30, nullable: true, options: ["default" => "En attente"])]
    private ?string $etatCommande = null;

    #[ORM\Column(length: 30, nullable: false)]
    private ?string $adresse = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: "id_client", referencedColumnName: "id")]
    private ?Utilisateur $idClient = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getModeLivraison(): ?string
    {
        return $this->modeLivraison;
    }

    public function setModeLivraison(?string $modeLivraison): static
    {
        $this->modeLivraison = $modeLivraison;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?string $modePaiement): static
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function getCoutTotale(): ?float
    {
        return $this->coutTotale;
    }

    public function setCoutTotale(?float $coutTotale): static
    {
        $this->coutTotale = $coutTotale;

        return $this;
    }

    public function getEtatCommande(): ?string
    {
        return $this->etatCommande;
    }

    public function setEtatCommande(?string $etatCommande): static
    {
        $this->etatCommande = $etatCommande;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = '$test';

        return $this;
    }

    public function getIdClient(): ?Utilisateur
    {
        return $this->idClient;
    }

    public function setIdClient(?Utilisateur $idClient): static
    {
        $this->idClient = $idClient;

        return $this;
    }
}
