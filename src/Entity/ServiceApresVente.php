<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ServiceApresVenteRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceApresVenteRepository::class)]
class ServiceApresVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idService", type: "integer", nullable: false)]
    private int $idService;

    #[Assert\NotBlank(message: "La description ne peut pas être vide")]
    #[ORM\Column(name: "description", type: "string", length: 200, nullable: false)]
    private string $description;

    #[Assert\NotBlank(message: "Le type ne peut pas être vide")]
    #[ORM\Column(name: "type", type: "string", length: 50, nullable: false)]
    private string $type;

    #[ORM\Column(name: "date", type: "datetime", nullable: false)]
    private \DateTimeInterface $date;

    #[ORM\Column(name: "status", type: "boolean", nullable: false)]
    private bool $status = false;



    #[ORM\Column(name: "idAchats", type: "integer", nullable: true)]
    private ?int $idAchats;

    #[ORM\Column(name: "idPartenaire", type: "integer", nullable: true)]
    private ?int $idPartenaire;

    public function getIdService(): int
    {
        return $this->idService;
    }

    public function setIdService(int $idService): void
    {
        $this->idService = $idService;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    public function getIdAchats(): int
    {
        return $this->idAchats;
    }

    public function setIdAchats(int $idAchats): void
    {
        $this->idAchats = $idAchats;
    }

    public function getIdPartenaire(): ?int
    {
        return $this->idPartenaire;
    }


    public function setIdPartenaire(int $idPartenaire): void
    {
        $this->idPartenaire = $idPartenaire;
    }



}
