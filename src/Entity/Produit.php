<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity]
#[Vich\Uploadable]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le nom du produit est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "Le nom ne peut pas dépasser 255 caractères.")]
    private string $nom;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    private string $description;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "La quantité est obligatoire.")]
    #[Assert\Positive(message: "La quantité doit être un nombre positif.")]
    private int $quantite;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private int $prix;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Veuillez sélectionner une catégorie.")]
    private string $categories;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateCreationProduit = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateModificationProduit = null;

    #[ORM\Column(type: 'boolean')]
    private bool $approved = false;

    #[Vich\UploadableField(mapping: "produit_images", fileNameProperty: "imageFileName")]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageFileName = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Commande $Commande = null;

    // Getter et setter pour chaque propriété

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getPrix(): int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getCategories(): string
    {
        return $this->categories;
    }

    public function setCategories(string $categories): self
    {
        $this->categories = $categories;
        return $this;
    }

    public function getDateCreationProduit(): ?\DateTimeInterface
    {
        return $this->dateCreationProduit;
    }

    public function setDateCreationProduit(?\DateTimeInterface $dateCreationProduit): self
    {
        // Vérifiez que la date est bien un objet DateTime ou DateTimeImmutable
        if ($dateCreationProduit !== null && !$dateCreationProduit instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException('La date doit être une instance de DateTimeInterface.');
        }

        $this->dateCreationProduit = $dateCreationProduit;
        return $this;
    }

    public function getDateModificationProduit(): ?\DateTimeInterface
    {
        return $this->dateModificationProduit;
    }

    public function setDateModificationProduit(?\DateTimeInterface $dateModificationProduit): self
    {
        // Vérifiez que la date est bien un objet DateTime ou DateTimeImmutable
        if ($dateModificationProduit !== null && !$dateModificationProduit instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException('La date doit être une instance de DateTimeInterface.');
        }

        $this->dateModificationProduit = $dateModificationProduit;
        return $this;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }
    
    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;
        return $this;
    }
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;
        return $this;
    }

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(?string $imageFileName): self
    {
        $this->imageFileName = $imageFileName;
        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->Commande;
    }

    public function setCommande(?Commande $Commande): self
    {
        $this->Commande = $Commande;
        return $this;
    }
}
