<?php

namespace App\Entity;

use App\Repository\ReservationEnchereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationEnchereRepository::class)]
/**
 * ReservationEnchere
 *
 * @ORM\Table(name="reservation_enchere", indexes={@ORM\Index(name="idEnchere", columns={"idEnchere"}), @ORM\Index(name="idUser", columns={"idUser"})})
 * @ORM\Entity
 */
class ReservationEnchere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idReservation")]
    private ?int $idreservation = null;


    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateReservation", type="date", nullable=true)
     */
    private $datereservation;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="confirmation", type="boolean", nullable=true)
     */
    private $confirmation;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     */
    private $iduser;

    /**
     * @var Encheres
     *
     * @ORM\ManyToOne(targetEntity="Encheres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idEnchere", referencedColumnName="idEnchere")
     * })
     */
    private $idenchere;

    public function getIdreservation(): int
    {
        return $this->idreservation;
    }

    public function setIdreservation(int $idreservation): void
    {
        $this->idreservation = $idreservation;
    }

    public function getDatereservation(): ?\DateTime
    {
        return $this->datereservation;
    }

    public function setDatereservation(?\DateTime $datereservation): void
    {
        $this->datereservation = $datereservation;
    }

    public function getConfirmation(): ?bool
    {
        return $this->confirmation;
    }

    public function setConfirmation(?bool $confirmation): void
    {
        $this->confirmation = $confirmation;
    }

    public function getIduser(): Utilisateur
    {
        return $this->iduser;
    }

    public function setIduser(Utilisateur $iduser): void
    {
        $this->iduser = $iduser;
    }

    public function getIdenchere(): Encheres
    {
        return $this->idenchere;
    }

    public function setIdenchere(Encheres $idenchere): void
    {
        $this->idenchere = $idenchere;
    }

    public function isConfirmation(): ?bool
    {
        return $this->confirmation;
    }
}
