<?php

namespace App\Entity;

use App\Repository\FieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FieldRepository::class)]
class Field
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

  
    #[ORM\Column]
    #[Assert\NotBlank(message: "Surface should not be empty")]
    #[Assert\Positive(message: "Surface must be a positive number")]
    #[Assert\Type(type: 'float', message: "Surface must be a valid number")]
    private ?float $surface = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Please enter a field name")]
    #[Assert\Length(max: 20, maxMessage: "Field name cannot exceed 20 characters")]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'fields')]
    private ?Farm $Farm = null;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'field', orphanRemoval: true)]
    private Collection $tasks;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Budget should not be empty")]
    #[Assert\PositiveOrZero(message: "Budget must be a positive number or zero")]
    #[Assert\Type(type: 'float', message: "Budget must be a valid number")]
    private ?float $budget = null;

    #[ORM\Column]
   
    private ?float $income = null;

    #[ORM\Column]
   
    private ?float $outcome = null;

    #[ORM\Column]

    private ?float $profit = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Description should not be empty")]
    #[Assert\Length(max: 255, maxMessage: "Description cannot exceed 255 characters")]
    private ?string $description = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Crop $Crop = null;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFarm(): ?Farm
    {
        return $this->Farm;
    }

    public function setFarm(?Farm $Farm): static
    {
        $this->Farm = $Farm;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setField($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getField() === $this) {
                $task->setField(null);
            }
        }

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

    public function getIncome(): ?float
    {
        return $this->income;
    }

    public function setIncome(float $income): static
    {
        $this->income = $income;

        return $this;
    }

    public function getOutcome(): ?float
    {
        return $this->outcome;
    }

    public function setOutcome(float $outcome): static
    {
        $this->outcome = $outcome;

        return $this;
    }

    public function getProfit(): ?float
    {
        return $this->profit;
    }

    public function setProfit(float $profit): static
    {
        $this->profit = $profit;

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
    public function getCrop(): ?Crop
    {
        return $this->Crop;
    }

    public function setCrop(?Crop $Crop): static
    {
        $this->Crop = $Crop;

        return $this;
    }
}
