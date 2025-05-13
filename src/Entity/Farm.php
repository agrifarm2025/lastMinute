<?php

namespace App\Entity;

use App\Repository\FarmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: FarmRepository::class)]
class Farm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank(message: "Location should not be empty")]
        #[Assert\Length(max: 255, maxMessage: "Location cannot exceed 255 characters")]
        private ?string $location = null;
    
        #[ORM\Column(length: 20)]
        #[Assert\NotBlank(message: "Name should not be empty")]
        #[Assert\Length(max: 20, maxMessage: "Name cannot exceed 20 characters")]
        private ?string $name = null;
    
        #[ORM\Column]
        #[Assert\NotBlank(message: "Surface should not be empty")]
        #[Assert\Positive(message: "Surface must be a positive number")]
        #[Assert\Type(type: 'float', message: "Surface must be a valid number")]
        private ?float $surface = null;
    
        /**
         * @var Collection<int, Field>
         */
        #[ORM\OneToMany(targetEntity: Field::class, mappedBy: 'Farm', orphanRemoval: true)]
        private Collection $fields;
    
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank(message: "Address should not be empty")]
        #[Assert\Length(max: 255, maxMessage: "Address cannot exceed 255 characters")]
        private ?string $adress = null;
    
        #[ORM\Column]
        #[Assert\NotBlank(message: "Budget should not be empty")]
        #[Assert\Positive(message: "Budget must be a positive number")]
        #[Assert\Type(type: 'float', message: "Budget must be a valid number")]
        private ?float $budget = null;
    
        #[ORM\Column(length: 20, nullable: true)]
        
        private ?string $weather = null;
    
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank(message: "Description should not be empty")]
        #[Assert\Length(max: 255, maxMessage: "Description cannot exceed 255 characters")]
        private ?string $description = null;
    
        #[ORM\Column]
        private ?bool $bir = null;
    
        #[ORM\Column]
        private ?bool $photovoltaic = null;
    
        #[ORM\Column]
        private ?bool $fence = null;
    
        #[ORM\Column]
        private ?bool $irrigation = null;
    
        #[ORM\Column]
        private ?bool $cabin = null;
    
     
    

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(float $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    /**
     * @return Collection<int, Field>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(Field $field): static
    {
        if (!$this->fields->contains($field)) {
            $this->fields->add($field);
            $field->setFarm($this);
        }

        return $this;
    }

    public function removeField(Field $field): static
    {
        if ($this->fields->removeElement($field)) {
            // set the owning side to null (unless already changed)
            if ($field->getFarm() === $this) {
                $field->setFarm(null);
            }
        }

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

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getWeather(): ?string
    {
        return $this->weather;
    }

    public function setWeather(?string $weather): static
    {
        $this->weather = $weather;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

   

    public function isBir(): ?bool
    {
        return $this->bir;
    }

    public function setBir(bool $bir): static
    {
        $this->bir = $bir;

        return $this;
    }

    public function isPhotovoltaic(): ?bool
    {
        return $this->photovoltaic;
    }

    public function setPhotovoltaic(bool $photovoltaic): static
    {
        $this->photovoltaic = $photovoltaic;

        return $this;
    }

    public function isFence(): ?bool
    {
        return $this->fence;
    }

    public function setFence(bool $fence): static
    {
        $this->fence = $fence;

        return $this;
    }

    public function isIrrigation(): ?bool
    {
        return $this->irrigation;
    }

    public function setIrrigation(bool $irrigation): static
    {
        $this->irrigation = $irrigation;

        return $this;
    }

    public function isCabin(): ?bool
    {
        return $this->cabin;
    }

    public function setCabin(bool $cabin): static
    {
        $this->cabin = $cabin;

        return $this;
    }
}
