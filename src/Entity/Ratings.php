<?php

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
/**
 * Ratings
 *
 * @ORM\Table(
 *     name="ratings",
 *     indexes={
 *         @ORM\Index(name="fk_user", columns={"id_user"}),
 *         @ORM\Index(name="fk_produit", columns={"id_produit"})
 *     }
 * )
 * @ORM\Entity
 */
class Ratings
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id_rating", type: "integer", nullable: false)]
    private int $idRating;

    #[ORM\Column(name: "rating", type: "float", precision: 10, scale: 0, nullable: false)]
    private float $rating;



    #[ORM\ManyToOne(targetEntity: Produits::class)]
    #[ORM\JoinColumn(name: "id_produit", referencedColumnName: "idProduit")]
    private Produits $produit;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id", nullable: true)]

    private ?Utilisateur $user = null;


    public function getIdRating(): int
    {
        return $this->idRating;
    }

    public function setIdRating(int $idRating): void
    {
        $this->idRating = $idRating;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): void
    {
        $this->rating = $rating;
    }


    public function getProduit(): Produits
    {
        return $this->produit;
    }

    public function setProduit(Produits $produit): void
    {
        $this->produit = $produit;
    }

    public function getUser(): Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): void
    {
        $this->user = $user;
    }
}
