<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column(name: "date_ajout", type: "date", nullable: false)]
    private \DateTime $dateAjout;

    #[ORM\Column(name: "id_client", type: "integer", nullable: true)]
    private ?int $idClient;

    #[ORM\Column(name: "id_produit", type: "integer", nullable: true)]
    private ?int $idProduit;

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

    public function setDateAjout(\DateTime $dateAjout): void
    {
        $this->dateAjout = $dateAjout;
    }

    public function getIdClient(): ?int
    {
        return $this->idClient;
    }

    public function setIdClient(?int $idClient): void
    {
        $this->idClient = $idClient;
    }

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function setIdProduit(?int $idProduit): void
    {
        $this->idProduit = $idProduit;
    }
}
