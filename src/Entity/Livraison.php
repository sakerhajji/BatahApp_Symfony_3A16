<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: LivraisonRepository::class)]
class Livraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idLivraison", type: "integer", nullable: false)]
    private int $idLivraison;

    #[ORM\Column(name: "dateLivraison", type: "date", nullable: false)]
    private \DateTimeInterface $dateLivraison;

    #[ORM\Column(name: "statut", type: "string", length: 50, nullable: false, options: ["default" => "en attente"])]
    private string $statut = 'en attente';

    #[ORM\ManyToOne(targetEntity: "Commands")]
    #[ORM\JoinColumn(name: "idCommande", referencedColumnName: "id", nullable: false)]
    private Commands $commande;


    #[ORM\Column(name: "idPartenaire",type: "integer" , nullable: true)]
    private ?int $partenaire;

    // Getters and setters
    public function getIdLivraison(): ?int
    {
        return $this->idLivraison;
    }

    public function getDateLivraison(): ?\DateTimeInterface
    {
        return $this->dateLivraison;
    }

    public function setDateLivraison(\DateTimeInterface $dateLivraison): self
    {
        $this->dateLivraison = $dateLivraison;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCommande(): ?Commands
    {
        return $this->commande;
    }

    public function setCommande(?Commands $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function getPartenaire(): ?int
    {
        return $this->partenaire;
    }

    public function setPartenaire(?int $partenaire): self
    {
        $this->partenaire = $partenaire;
        return $this;
    }
}