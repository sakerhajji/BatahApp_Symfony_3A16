<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitsRepository::class)]
class Produits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idProduit", type: "integer", nullable: false)]
    private int $idProduit;

    #[ORM\Column(name: "type", type: "string", length: 300, nullable: false)]
    private string $type;

    #[ORM\Column(name: "description", type: "string", length: 300, nullable: false)]
    private string $description;

    #[ORM\Column(name: "prix", type: "float", nullable: false)]
    private float $prix;

    #[ORM\Column(name: "labelle", type: "string", length: 300, nullable: false)]
    private string $labelle;

    #[ORM\Column(name: "status", type: "string", length: 255, nullable: false)]
    private string $status;

    #[ORM\Column(name: "periodeGarantie", type: "integer", nullable: false)]
    #[Assert\PositiveOrZero]
    private int $periodeGarantie;

    #[ORM\Column(name: "photo", type: "string", length: 255, nullable: false)]
    private string $photo;

    #[ORM\Column(name: "video", type: "string", length: 250, nullable: true)]
    #[Assert\Url]
    private ?string $video;


    #[ORM\Column(name: "localisation", type: "string", length: 255, nullable: false)]
    private string $localisation;


    #[ORM\Column(name: "nombreDeVues", type: "integer")]
    private int $nombreDeVues;
    /*

    #[ORM\Column(name: "likes", type: "integer", nullable: true, options: ["default" => 0])]
    private ?int $likes;

    #[ORM\Column(name: "dislikes", type: "integer", nullable: true, options: ["default" => 0])]
    private ?int $dislikes;
*/

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, cascade: ["persist"])]
    #[ORM\JoinColumn(name: "idUtilisateur", referencedColumnName: "id", nullable: true)]
    private ?Utilisateur $idUtilisateur;


    public function __construct()
    {
        $this->nombreDeVues = 0; // Initialiser à zéro
        //$this->likes = 0;
        //$this->dislikes = 0;

        $this->images = new ArrayCollection();
    }




    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="Produits", orphanRemoval=true, cascade={"persist"})
     */
    private $images;


    /**
     * @ORM\OneToMany(targetEntity=Encheres::class, mappedBy="idProduit")
     */
    private $encheres;

    /**
     * @ORM\OneToMany(targetEntity=Views::class, mappedBy="idProduit")
     */
    private $vues;
    public function addView(Views $view): self
    {
        if (!$this->vues->contains($view)) {
            $this->vues[] = $view;
            // Incrémenter le nombre de vues
            $this->nombreDeVues++;
        }

        return $this;
    }

    /*
    public function addLike(): self
    {
        $this->likes++;
        return $this;
    }

    public function addDislike(): self
    {
        $this->dislikes++;
        return $this;
    }

    public function incrementLikes(): void
    {
        $this->likes++;
    }

    public function incrementDislikes(): void
    {
        $this->dislikes++;
    }
*/

    /**
     * @return Collection|Encheres[]|null
     */
    public function getEncheres(): ?Collection
    {
        return $this->encheres;
    }

    /**
     * @return Collection|Views[]|null
     */
    public function getViews(): ?Collection
    {
        return $this->vues;
    }


    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images ?: new ArrayCollection();
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduits($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduits() === $this) {
                $image->setProduits(null);
            }
        }

        return $this;
    }
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $imageFile = null;

    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    public function setImageFile(?string $imageFile): void
    {
        $this->imageFile = $imageFile;
    }


    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getLabelle(): ?string
    {
        return $this->labelle;
    }

    public function setLabelle(string $labelle): self
    {
        $this->labelle = $labelle;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPeriodeGarantie(): ?int
    {
        return $this->periodeGarantie;
    }

    public function setPeriodeGarantie(int $periodeGarantie): self
    {
        $this->periodeGarantie = $periodeGarantie;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getNombreDeVues(): int
    {
        return $this->nombreDeVues;
    }

    public function setNombreDeVues(int $nombreDeVues): self
    {
        $this->nombreDeVues = $nombreDeVues;

        return $this;
    }
    /*
    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;
        return $this;
    }

    public function getDislikes(): ?int
    {
        return $this->dislikes;
    }

    public function setDislikes(int $dislikes): void
    {
        $this->dislikes = $dislikes;
    }
*/
    public function getIdUtilisateur(): ?Utilisateur
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?Utilisateur $idUtilisateur): self
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }
    public function __toString()
    {
        return (string) $this->getIdProduit();
    }
}
