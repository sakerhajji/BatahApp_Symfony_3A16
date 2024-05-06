<?php

namespace App\Entity;

use App\Repository\ViewsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ViewsRepository::class)]
class Views
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idViews")]
    private int $idViews;



    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "id")]

    private $utilisateur;


    #[ORM\ManyToOne(targetEntity: Produits::class)]
    #[ORM\JoinColumn(name: "produit_id", referencedColumnName: "idProduit")]
    private $produit;



    #[ORM\Column(name: "likes", type: "integer", nullable: true, options: ["default" => 0])]
    private ?int $likes;

    #[ORM\Column(name: "dislikes", type: "integer", nullable: true, options: ["default" => 0])]
    private ?int $dislikes;


    public function __construct()
    {
        $this->likes = 0;
        $this->dislikes = 0;
    }


    public function getIdViews(): ?int
    {
        return $this->idViews;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getProduit(): ?Produits
    {
        return $this->produit;
    }

    public function setProduit(?Produits $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
    public function incrementLikes(): void
    {
        $this->likes++;
    }

    public function incrementDislikes(): void
    {
        $this->dislikes++;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;
        return $this;
    }

    public function getDislikes(): int
    {
        return $this->dislikes;
    }

    public function setDislikes(int $dislikes): void
    {
        $this->dislikes = $dislikes;
    }
}
