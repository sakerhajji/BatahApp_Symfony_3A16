<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PartenairesRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PartenairesRepository::class)]
#[Vich\Uploadable]
class Partenaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idPartenaire", type: "integer", nullable: false)]
    private int $idpartenaire;
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide")]
    #[Assert\Length(max: 20, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères")]
    #[ORM\Column(name: "nom", type: "string", length: 20, nullable: false)]
    private string $nom;
    #[Assert\NotBlank(message: "Le type ne peut pas être vide")]
    #[Assert\Choice(choices: ["voiture", "maison", "terrain","livraison"], message: "Le type doit être voiture, maison ou terrain")]
    #[ORM\Column(name: "type", type: "string", length: 20, nullable: false)]
    private string $type;
    #[Assert\NotBlank(message: "L'adresse ne peut pas être vide")]
    #[Assert\Length(max: 20, maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères")]
    #[ORM\Column(name: "adresse", type: "string", length: 20, nullable: false)]
    private string $adresse;
    #[Assert\NotBlank(message: "Le téléphone ne peut pas être vide")]
    #[Assert\Regex(pattern: "/^\d{8}$/", message: "Le téléphone doit être composé de 8 chiffres")]
    #[ORM\Column(name: "telephone", type: "integer", nullable: false)]
    private int $telephone;
    #[Assert\NotBlank(message: "L'email ne peut pas être vide")]
    #[Assert\Email(message: "L'email doit être valide")]
    #[ORM\Column(name: "email", type: "string", length: 50, nullable: false)]
    private string $email;

    #[ORM\Column(name: "logo", type: "string", length: 200, nullable: true)]
    private ?string $logo=null;
    #[ORM\Column(name: "points", type: "integer", nullable: true)]
    private ?int $points = 0;

    private ?File $logoFile ;


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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): void
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
    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function setLogoFile(?File $logoFile): void
    {
        $this->logoFile = $logoFile;
    }


    public function __toString(): string
    {
        return "ID: {$this->idpartenaire}, Nom: {$this->nom}, Type: {$this->type}, Adresse: {$this->adresse}, Telephone: {$this->telephone}, Email: {$this->email}, Logo: {$this->logo}, Points: {$this->points}";
    }


}

