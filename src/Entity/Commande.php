<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?int $prix = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "La type commande est obligatoire.")]
    private string $typeCommande;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 5,max:100, minMessage: "La description doit contenir au moins 5 caractères.")]
    private ?string $adress = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    #[Assert\NotBlank(message: "Le paiement est obligatoire.")]
    private array $paiment = [];

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creation_commande = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class, inversedBy: 'commandes')]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getTypeCommande(): ?string
    {
        return $this->typeCommande;
    }

    public function setTypeCommande(string $typeCommande): self
    {
        $this->typeCommande = $typeCommande;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPaiment(): array
    {
        return $this->paiment;
    }

    public function setPaiment(array $paiment): self
    {
        $this->paiment = $paiment;

        return $this;
    }

    public function getDateCreationCommande(): ?\DateTimeInterface
    {
        return $this->date_creation_commande;
    }

    public function setDateCreationCommande(\DateTimeInterface $date_creation_commande): static
    {
        $this->date_creation_commande = $date_creation_commande;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            // Ensure reverse relationship is set
            $produit->addCommande($this); // Add the command to the product
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            // If the produit is removed, remove the reverse relation
            $produit->removeCommande($this);
        }

        return $this;
    }
}
