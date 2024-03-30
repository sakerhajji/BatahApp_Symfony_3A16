<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\ORM\Mapping as ORM;
use Monolog\Handler\Curl\Util;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
/**
 * Basket
 *
 * @ORM\Table(
 *     name="basket",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="UNIQ_2246507BE173B1B8", columns={"id_client"}),
 *         @ORM\UniqueConstraint(name="UNIQ_2246507BF7384557", columns={"id_produit"})
 *     }
 * )
 * @ORM\Entity
 */
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idBasket", type: "integer", nullable: false)]
    private int $idbasket;

    #[ORM\Column(name: "remise", type: "string", length: 255, nullable: false)]
    private string $remise;

    #[ORM\Column(name: "date_ajout", type: "datetime", nullable: false)]
    private \DateTime $dateAjout;

    #[ORM\ManyToOne(targetEntity: "Utilisateur")]
    #[ORM\JoinColumn(name: "id_client", referencedColumnName: "id")]
    private $idClient;


    #[ORM\ManyToOne(targetEntity: "Produits")]
    #[ORM\JoinColumn(name: "id_produit", referencedColumnName: "idProduit")]
    private $idProduit;

    public function getIdbasket(): int
    {
        return $this->idbasket;
    }

    public function setIdbasket(int $idbasket): void
    {
        $this->idbasket = $idbasket;
    }

    public function getRemise(): string
    {
        return $this->remise;
    }

    public function setRemise(string $remise): void
    {
        $this->remise = $remise;
    }

    public function getDateAjout(): \DateTime
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): self
    {
        $this->dateAjout = $dateAjout;

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

    public function getIdProduit(): ?Produits
    {
        return $this->idProduit;
    }

    public function setIdProduit(?Produits $idProduit): self
    {
        $this->idProduit = $idProduit;
        return $this;
    }
}
