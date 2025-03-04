<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // Importez les contraintes de validation

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le champ 'rate' ne peut pas être vide.")] // Le champ ne doit pas être vide
    #[Assert\Range(
        min: 0,
        max: 5,
        notInRangeMessage: "La note doit être comprise entre {{ min }} et {{ max }}."
    )] // La note doit être entre 0 et 5
    private ?string $rate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le champ 'commentaire' ne peut pas être vide.")] // Le champ ne doit pas être vide
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le commentaire doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le commentaire ne peut pas dépasser {{ limit }} caractères."
    )] // Le commentaire doit avoir entre 5 et 255 caractères
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'commentaire')]
    private ?Article $article = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }
}