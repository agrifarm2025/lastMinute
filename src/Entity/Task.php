<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


        #[ORM\Column(length: 20)]
        #[Assert\NotBlank(message: "Task name should not be empty")]
        #[Assert\Length(max: 20, maxMessage: "Task name cannot exceed 20 characters")]
        private ?string $name = null;
    
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank(message: "Description should not be empty")]
        #[Assert\Length(max: 255, maxMessage: "Description cannot exceed 255 characters")]
        private ?string $description = null;
    
        #[ORM\Column(length: 10)]
        #[Assert\NotBlank(message: "Status should not be empty")]
        private ?string $status = null;
    
        #[ORM\Column(type: Types::DATE_MUTABLE)]
        #[Assert\NotBlank(message: "Date should not be empty")]
        #[Assert\GreaterThan("today", message: "Date must be in the future")]
        private ?\DateTimeInterface $date = null;
    
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank(message: "Resource should not be empty")]
        private ?string $ressource = null;
    
        #[ORM\Column(length: 20)]
        #[Assert\NotBlank(message: "Responsable should not be empty")]
        private ?string $responsable = null;
    
        #[ORM\ManyToOne(inversedBy: 'tasks')]
        private ?Field $field = null;
    
        #[ORM\Column(length: 10)]
        #[Assert\NotBlank(message: "Priority should not be empty")]
        private ?string $priority = null;
    
        #[ORM\Column(length: 30)]
        #[Assert\NotBlank(message: "Estimated duration should not be empty")]
        private ?string $estimated_duration = null;
    
        #[ORM\Column(type: Types::DATE_MUTABLE)]
        #[Assert\NotBlank(message: "Deadline should not be empty")]
        private ?\DateTimeInterface $deadline = null;
    
        #[ORM\Column]
        #[Assert\NotBlank(message: "Number of workers should not be empty")]
        #[Assert\Positive(message: "Workers count must be a positive number")]
        private ?int $workers = null;
    
        #[ORM\Column(type: Types::DATE_MUTABLE)]
        private ?\DateTimeInterface $last_updated = null;
    
        #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
        #[Assert\PositiveOrZero(message: "Payment must be a positive number or zero")]
        private ?float $payment_worker = null;
        public function __construct(){
            
        }
        public function Task(
            string $name,
            string $description,
            string $status,
            \DateTimeInterface $date,
            string $ressource,
            string $responsable,
            ?Field $field,
            string $priority,
            string $estimated_duration,
            \DateTimeInterface $deadline,
            int $workers,
            ?\DateTimeInterface $last_updated,
            ?float $payment_worker
        ) {
            $this->name = $name;
            $this->description = $description;
            $this->status = $status;
            $this->date = $date;
            $this->ressource = $ressource;
            $this->responsable = $responsable;
            $this->field = $field;
            $this->priority = $priority;
            $this->estimated_duration = $estimated_duration;
            $this->deadline = $deadline;
            $this->workers = $workers;
            $this->last_updated = $last_updated;
            $this->payment_worker = $payment_worker;
    
            $this->setTotal();
        }
        /**
         * Custom validation for deadline to be higher than date.
         */
        #[Assert\Callback]
        public function validateDeadline(ExecutionContextInterface $context)
        {
            $defaultDate = new \DateTime('1970-01-01'); // The default date as a DateTime object
            if ($this->date == $defaultDate && $this->deadline == $defaultDate ) {

            if ($this->date == $defaultDate) {
                $context->buildViolation("Task date should not be empty")
                    ->atPath('date')
                    ->addViolation();
            }
        
            if ($this->deadline == $defaultDate) {
                $context->buildViolation("Deadline should not be empty")
                    ->atPath('deadline')
                    ->addViolation();
            }}
        else{
                if ($this->deadline <= $this->date) {
                    $context->buildViolation("Deadline must be later than the task date")
                        ->atPath('deadline')
                        ->addViolation();
                }
            }
        }
    
    

    #[ORM\Column]
    private ?float $total = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getRessource(): ?string
    {
        return $this->ressource;
    }

    public function setRessource(string $ressource): static
    {
        $this->ressource = $ressource;

        return $this;
    }

    public function getResponsable(): ?string
    {
        return $this->responsable;
    }

    public function setResponsable(string $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function setField(?Field $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getEstimatedDuration(): ?string
    {
        return $this->estimated_duration;
    }

    public function setEstimatedDuration(string $estimated_duration): static
    {
        $this->estimated_duration = $estimated_duration;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getWorkers(): ?int
    {
        return $this->workers;
    }

    public function setWorkers(int $workers): static
    {
        $this->workers = $workers;

        return $this;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->last_updated;
    }

    public function setLastUpdated(\DateTimeInterface $last_updated): static
    {
        $this->last_updated = $last_updated;

        return $this;
    }

    public function getPaymentWorker(): ?float
    {
        return $this->payment_worker;
    }

    public function setPaymentWorker(float $payment_worker): static
    {
        $this->payment_worker = $payment_worker;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    
        public function setTotal(): void
        {
            // Ensure payment_worker and workers are set before calculation
            if ($this->payment_worker !== null && $this->workers !== null) {
                $this->total = $this->payment_worker * $this->workers;
            }
        }
    
}
