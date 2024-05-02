<?php

namespace App\Entity;

use App\Repository\AchatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AchatsRepository::class)]
class Achats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idAchats", type: "integer", nullable: false)]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function __toString(): string
    {
        return (string) $this->id; // You can modify this to return any property of the Achats entity you want to display
    }
}
