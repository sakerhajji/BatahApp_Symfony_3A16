<?php
namespace App\Entity;
use App\Repository\AvisLivraisonRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass:AvisLivraisonRepository::class)]
#[ORM\Table(name:"avisLivraison")]
class AvisLivraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idAvis", type: "integer")]
    private int $idAvis;

    #[ORM\ManyToOne(targetEntity: "Livraison")]
    #[ORM\JoinColumn(name: "idLivraison", referencedColumnName: "idLivraison", nullable: false)]
    private Livraison $livraison;

    #[ORM\Column(name: "commentaire", type: "string", length: 200)]
    private string $commentaire;

// Getters and setters
    public function getIdAvis(): ?int
    {
        return $this->idAvis;
    }

    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(Livraison $livraison): self
    {
        $this->livraison = $livraison;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }
}
