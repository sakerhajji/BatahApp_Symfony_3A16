<?php

namespace App\Entity;

use App\Repository\ServiceApresVenteRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceApresVenteRepository::class)]
class ServiceApresVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idService", type: "integer", nullable: false)]
    private ?int $idService = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]+$/',
        message: 'Name must contain only letters'
    )]
    #[Assert\NotBlank(message: 'description  cannot be blank')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'type cannot be blank')]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)] // Change from DATE_MUTABLE to DATETIME_MUTABLE
    #[Assert\NotBlank(message: 'date cannot be blank')]
    private ?DateTimeInterface $date = null;


    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\ManyToOne(targetEntity: Achats::class)]
    #[ORM\JoinColumn(name: "idAchats", referencedColumnName: "idAchats", nullable: false)] // Set nullable to false
    private Achats $achats;

    #[ORM\ManyToOne(targetEntity: Partenaires::class, inversedBy: "services")]
    #[ORM\JoinColumn(name: "idPartenaire", referencedColumnName: "idPartenaire")]
    private ?Partenaires $idPartenaire = null;

    public function getIdService(): ?int
    {
        return $this->idService;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Achats|null
     */
    public function getAchats(): ?Achats
    {
        return $this->achats;
    }

    /**
     * @param Achats|null $achats
     */
    public function setAchats(?Achats $achats): void
    {
        $this->achats = $achats;
    }

    /**
     * @return Partenaires|null
     */
    public function getIdPartenaire(): ?Partenaires
    {
        return $this->idPartenaire;
    }

    /**
     * @param Partenaires|null $idPartenaire
     */
    public function setIdPartenaire(?Partenaires $idPartenaire): void
    {
        $this->idPartenaire = $idPartenaire;
    }
}
