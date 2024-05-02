<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    private ?int $id;


    #[ORM\Column(name: "idGoogle", type: "string", length: 255, nullable: true)]
    private ?string $idgoogle = null;

    #[ORM\Column(name: "nomUtilisateur", type: "string", length: 30, nullable: true)]
    private ?string $nomutilisateur = null;

    #[ORM\Column(name: "prenomUtilisateur", type: "string", length: 50, nullable: true)]
    private ?string $prenomutilisateur = null;

    #[ORM\Column(name: "sexe", type: "string", length: 1, nullable: true, options: ["fixed" => true])]
    private ?string $sexe = null;

    #[ORM\Column(name: "dateDeNaissance", type: "date", nullable: true)]
    private ?\DateTimeInterface $datedenaissance = null;

    #[ORM\Column(name: "adresseEmail", type: "string", length: 100, nullable: true)]
    private ?string $adresseemail = null;

    #[ORM\Column(name: "motDePasse", type: "string", length: 30, nullable: true)]
    private ?string $motdepasse = null;

    #[ORM\Column(name: "adressePostale", type: "string", length: 60, nullable: true)]
    private ?string $adressepostal = null;

    #[ORM\Column(name: "numeroTelephone", type: "string", length: 30, nullable: true)]
    private ?string $numerotelephone = null;

    #[ORM\Column(name: "numeroCin", type: "string", length: 9, nullable: true)]
    private ?string $numerocin = null;

    #[ORM\Column(name: "pays", type: "string", length: 50, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(name: "nbrProduitAchat", type: "integer", nullable: true)]
    private ?int $nbrproduitachat = null;

    #[ORM\Column(name: "nbrProduitVendu", type: "integer", nullable: true)]
    private ?int $nbrproduitvendu = null;

    #[ORM\Column(name: "nbrProduit", type: "integer", nullable: true)]
    private ?int $nbrproduit = null;

    #[ORM\Column(name: "nbrPoint", type: "integer", nullable: true)]
    private ?int $nbrpoint = null;

    #[ORM\Column(name: "languePreferree", type: "string", length: 50, nullable: true)]
    private ?string $languepreferree = null;

    #[ORM\Column(name: "evaluationUtilisateur", type: "float", precision: 10, scale: 0, nullable: true)]
    private ?float $evaluationutilisateur = null;

    #[ORM\Column(name: "statutVerificationCompte", type: "boolean", nullable: true)]
    private ?bool $statutverificationcompte = null;

    #[ORM\Column(name: "avatar", type: "string", length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(name: "dateInscription", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $dateinscription = null;

    #[ORM\Column(name: "role", type: "string", length: 1, nullable: true, options: ["fixed" => true])]
    private ?string $role = "U";

    /**
     * @ORM\OneToMany(targetEntity=Produits::class, mappedBy="idUtilisateur")
     */
    private $produits;

    /**
     * @ORM\OneToMany(targetEntity=Views::class, mappedBy="utilisateur")
     */
    private $views;

    #[ORM\OneToMany(mappedBy: 'idClient', targetEntity: Comment::class)]
    private Collection $comments;


    public function __construct()
    {
        $this->dateinscription = new \DateTime(); // Définit la date actuelle
        $this->produits = new ArrayCollection();
        $this->views = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }
    /**
     * @return Collection|Views[]
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(Views $view): self
    {
        if (!$this->views->contains($view)) {
            $this->views[] = $view;
            $view->setUtilisateur($this);
        }

        return $this;
    }

    public function removeView(Views $view): self
    {
        if ($this->views->removeElement($view)) {
            // set the owning side to null (unless already changed)
            if ($view->getUtilisateur() === $this) {
                $view->setUtilisateur(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|Produits[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produits $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setIdUtilisateur($this);
        }

        return $this;
    }

    public function removeProduit(Produits $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getIdUtilisateur() === $this) {
                $produit->setIdUtilisateur(null);
            }
        }

        return $this;
    }



    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getIdgoogle(): ?string
    {
        return $this->idgoogle;
    }

    public function setIdgoogle(?string $idgoogle): void
    {
        $this->idgoogle = $idgoogle;
    }

    public function getNomutilisateur(): ?string
    {
        return $this->nomutilisateur;
    }

    public function setNomutilisateur(?string $nomutilisateur): void
    {
        $this->nomutilisateur = $nomutilisateur;
    }

    public function getPrenomutilisateur(): ?string
    {
        return $this->prenomutilisateur;
    }

    public function setPrenomutilisateur(?string $prenomutilisateur): void
    {
        $this->prenomutilisateur = $prenomutilisateur;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): void
    {
        $this->sexe = $sexe;
    }

    public function getDatedenaissance(): ?\DateTimeInterface
    {
        return $this->datedenaissance;
    }

    public function setDatedenaissance($datedenaissance): void
    {
        if (is_string($datedenaissance)) {
            $this->datedenaissance = new \DateTime($datedenaissance);
        } else {
            $this->datedenaissance = $datedenaissance;
        }
    }

    public function getAdresseemail(): ?string
    {
        return $this->adresseemail;
    }

    public function setAdresseemail(?string $adresseemail): void
    {
        $this->adresseemail = $adresseemail;
    }

    public function getMotdepasse(): ?string
    {
        return $this->motdepasse;
    }

    public function setMotdepasse(?string $motdepasse): void
    {
        $this->motdepasse = $motdepasse;
    }

    public function getAdressepostale(): ?string
    {
        return $this->adressepostal;
    }

    public function setAdressepostale(?string $adressepostale): void
    {
        $this->adressepostal = $adressepostale;
    }

    public function getNumerotelephone(): ?string
    {
        return $this->numerotelephone;
    }

    public function setNumerotelephone(?string $numerotelephone): void
    {
        $this->numerotelephone = $numerotelephone;
    }

    public function getNumerocin(): ?string
    {
        return $this->numerocin;
    }

    public function setNumerocin(?string $numerocin): void
    {
        $this->numerocin = $numerocin;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): void
    {
        $this->pays = $pays;
    }

    public function getNbrproduitachat(): ?int
    {
        return $this->nbrproduitachat;
    }

    public function setNbrproduitachat(?int $nbrproduitachat): void
    {
        $this->nbrproduitachat = $nbrproduitachat;
    }

    public function getNbrproduitvendu(): ?int
    {
        return $this->nbrproduitvendu;
    }

    public function setNbrproduitvendu(?int $nbrproduitvendu): void
    {
        $this->nbrproduitvendu = $nbrproduitvendu;
    }

    public function getNbrproduit(): ?int
    {
        return $this->nbrproduit;
    }

    public function setNbrproduit(?int $nbrproduit): void
    {
        $this->nbrproduit = $nbrproduit;
    }

    public function getNbrpoint(): ?int
    {
        return $this->nbrpoint;
    }

    public function setNbrpoint(?int $nbrpoint): void
    {
        $this->nbrpoint = $nbrpoint;
    }

    public function getLanguepreferree(): ?string
    {
        return $this->languepreferree;
    }

    public function setLanguepreferree(?string $languepreferree): void
    {
        $this->languepreferree = $languepreferree;
    }

    public function getEvaluationutilisateur(): ?float
    {
        return $this->evaluationutilisateur;
    }

    public function setEvaluationutilisateur(?float $evaluationutilisateur): void
    {
        $this->evaluationutilisateur = $evaluationutilisateur;
    }

    public function getStatutverificationcompte(): ?bool
    {
        return $this->statutverificationcompte;
    }

    public function setStatutverificationcompte(?bool $statutverificationcompte): void
    {
        $this->statutverificationcompte = $statutverificationcompte;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getDateinscription(): ?\DateTimeInterface
    {
        return $this->dateinscription;
    }

    public function setDateinscription(?\DateTimeInterface $dateinscription): void
    {
        $this->dateinscription = $dateinscription;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): void
    {
        $this->role = $role;
    }
    public function __toString()
    {
        return $this->getId();
    }



    public function getUserIdentifier()
    {
    }
    public function getPassword(): ?string
    {
        return $this->motdepasse; // Assuming your password property is named 'password'
    }

    // Implement other methods required by UserInterface
    public function getSalt()
    {
        // Not needed if you're using bcrypt or a modern hashing algorithm
    }

    public function getUsername()
    {
        // You can return any unique identifier for the user, for example, the email
        return $this->adresseemail;
    }

    public function getRoles()
    {
        // You can define custom logic here to determine user roles
        // For simplicity, let's return the role stored in the entity
        return [$this->role];
    }

    public function eraseCredentials()
    {
        // If you store any sensitive data on the user, clear it here
        // This method is called after the user is authenticated
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setIdClient($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getIdClient() === $this) {
                $comment->setIdClient(null);
            }
        }

        return $this;
    }
}
