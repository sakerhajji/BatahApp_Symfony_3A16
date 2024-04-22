<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idImage")]
    private int $idImage;

    #[ORM\Column(name: "url", type: "string", length: 250, nullable: false)]
    private string $url;

    #[ORM\ManyToOne(targetEntity: Produits::class)]
    #[ORM\JoinColumn(name: "idProduits", referencedColumnName: "idProduit")]
    private ?Produits $produits;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: "images")]
    #[ORM\JoinColumn(name: "idLocations", referencedColumnName: "idLocation")]
    private ?Location $location;

    public function getIdImage(): int
    {
        return $this->idImage;
    }

    public function setIdImage(int $idImage): void
    {
        $this->idImage = $idImage;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getProduits(): ?Produits
    {
        return $this->produits;
    }

    public function setProduits(?Produits $produits): void
    {
        $this->produits = $produits;
    }



    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }
}
