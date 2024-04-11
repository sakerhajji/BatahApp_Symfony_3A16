<?php

namespace App\Entity;

use App\Repository\CommandsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandsRepository::class)]
class Commands
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(name: "id", type: "integer", nullable: false)]
private int $id;

#[ORM\Column(name: "date_commande", type: "datetime", nullable: false, options: ["default" => "CURRENT_TIMESTAMP"])]
private \DateTimeInterface $dateCommande;

#[ORM\Column(name: "mode_livraison", type: "string", length: 30, nullable: true)]
private ?string $modeLivraison;

#[ORM\Column(name: "mode_paiement", type: "string", length: 30, nullable: true)]
private ?string $modePaiement;

#[ORM\Column(name: "cout_totale", type: "float", precision: 10, scale: 0, nullable: true)]
private ?float $coutTotale;

#[ORM\Column(name: "etat_commande", type: "string", length: 30, nullable: true, options: ["default" => "En attente"])]
private ?string $etatCommande;

#[ORM\Column(name: "adresse", type: "string", length: 30, nullable: false)]
private string $adresse;

#[ORM\ManyToOne(targetEntity: "Utilisateur", cascade: ["persist"])]
#[ORM\JoinColumn(name: "id_client", referencedColumnName: "id")]
private ?Utilisateur $idClient;

public function getId(): ?int
{
return $this->id;
}

public function getDateCommande(): ?\DateTimeInterface
{
return $this->dateCommande;
}

public function setDateCommande(\DateTimeInterface $dateCommande): self
{
$this->dateCommande = $dateCommande;
return $this;
}

public function getModeLivraison(): ?string
{
return $this->modeLivraison;
}

public function setModeLivraison(?string $modeLivraison): self
{
$this->modeLivraison = $modeLivraison;
return $this;
}

public function getModePaiement(): ?string
{
return $this->modePaiement;
}

public function setModePaiement(?string $modePaiement): self
{
$this->modePaiement = $modePaiement;
return $this;
}

public function getCoutTotale(): ?float
{
return $this->coutTotale;
}

public function setCoutTotale(?float $coutTotale): self
{
$this->coutTotale = $coutTotale;
return $this;
}

public function getEtatCommande(): ?string
{
return $this->etatCommande;
}

public function setEtatCommande(?string $etatCommande): self
{
$this->etatCommande = $etatCommande;
return $this;
}

public function getAdresse(): ?string
{
return $this->adresse;
}

public function setAdresse(string $adresse): self
{
$this->adresse = $adresse;
return $this;
}

public function getIdClient(): ?Utilisateur
{
return $this->idClient;
}

public function setIdClient(?Utilisateur $idClient): self
{
$this->idClient = $idClient;
return $this;
}
}
