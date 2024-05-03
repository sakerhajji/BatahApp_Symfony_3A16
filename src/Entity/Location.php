<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idLocation")]
    private int $idLocation;

    #[Assert\NotBlank(message: 'Le type ne peut pas être vide.')]
    #[ORM\Column(name: 'type', type: 'string', length: 300, nullable: false)]
    private  $type;


    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    #[ORM\Column(name: 'description', type: 'string', length: 300, nullable: false)]
    private  $description;

    #[Assert\NotBlank(message: 'Le prix ne peut pas être vide.')]
    #[Assert\Type(type: 'float', message: 'Le prix doit être un nombre.')]
    #[ORM\Column(name: 'prix', type: 'float', nullable: false)]
    private $prix;

    #[Assert\NotBlank(message: "L'adresse ne peut pas être vide.")]
    #[ORM\Column(name: 'adresse', type: 'string', length: 300, nullable: false)]
    private ?string $adresse;

    #[Assert\NotBlank(message: 'La disponibilité ne peut pas être vide.')]
    #[ORM\Column(name: 'disponibilite', type: 'string', length: 255, nullable: false)]
    private ?string $disponibilite;

        #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
        #[ORM\JoinColumn(name: "id", referencedColumnName: "id")]
        private Utilisateur $id;

        #[ORM\Column(name: "likes", type: "integer", nullable: true, options: ["default" => 0])]
        private ?int $likes;


    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="location", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->likes = 0;
    }

    public function incrementLikes(): void
    {
        $this->likes++;
    }


    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;
        return $this;
    }


    public function getIdLocation(): ?int
    {
        return $this->idLocation;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(?string $disponibilite): self
    {
        $this->disponibilite = $disponibilite;
        return $this;
    }

    public function getId(): ?Utilisateur
    {
        return $this->id;
    }

    public function setId(?Utilisateur $id): self
    {
        $this->id = $id;
        return $this;
    }


    public function __toString(): string
    {
        return (string) $this->getIdLocation();
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
            $image->setLocation($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getLocation() === $this) {
                $image->setLocation(null);
            }
        }

        return $this;
    }
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $imageFile;

    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    public function setImageFile(?string $imageFile): void
    {
        $this->imageFile = $imageFile;
    }
}
