<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PartenairesRepository;

#[ORM\Entity(repositoryClass: PartenairesRepository::class)]
class Partenaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idPartenaire", type: "integer", nullable: false)]
    private int $idPartenaire;


    #[ORM\Column(name: "nom", type: "string", length: 20, nullable: false)]
    private string $nom;

    #[ORM\Column(name: "type", type: "string", length: 20, nullable: false)]
    private string $type;

    #[ORM\Column(name: "adresse", type: "string", length: 20, nullable: false)]
    private string $adresse;

    #[ORM\Column(name: "telephone", type: "integer", nullable: false)]
    private int $telephone;

    #[ORM\Column(name: "email", type: "string", length: 50, nullable: false)]
    private string $email;

    #[ORM\Column(name: "logo", type: "string", length: 200, nullable: false)]
    private string $logo;

    #[ORM\Column(name: "points", type: "integer", nullable: true)]
    private ?int $points = 0;



    public function getIdpartenaire(): int
    {
        return $this->idpartenaire;
    }

    public function setIdpartenaire(int $idpartenaire): void
    {
        $this->idpartenaire = $idpartenaire;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getTelephone(): int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): void
    {
        $this->points = $points;
    }



    public function __toString(): string
    {
        return $this->nom;
    }



}

