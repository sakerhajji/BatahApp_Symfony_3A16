<?php

namespace App\Entity;

use App\Repository\CommandArticlesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandArticlesRepository::class)]
class CommandArticles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "article_id", referencedColumnName: "idProduit")]
    private ?Produits $article = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "command_id", referencedColumnName: "id")]
    private ?Commands $command = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Produits
    {
        return $this->article;
    }

    public function setArticle(?Produits $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getCommand(): ?Commands
    {
        return $this->command;
    }

    public function setCommand(?Commands $command): static
    {
        $this->command = $command;

        return $this;
    }
}
