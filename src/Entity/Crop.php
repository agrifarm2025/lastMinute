<?php
namespace App\Entity;

use App\Repository\CropRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CropRepository::class)]
class Crop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "La crop event est obligatoire.")]
    #[Assert\Length(
        min: 5,
        max: 30,
        minMessage: "La crop event doit contenir au moins 5 caractères.",
        maxMessage: "La crop event ne peut pas dépasser 30 caractères."
    )]
    private ?string $crop_event = null;

    #[ORM\Column(length: 30)]
    private ?string $type_crop = null;

    #[ORM\Column(length: 20)]
    private ?string $methode_crop = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de plantation est obligatoire.")]
    private ?\DateTimeInterface $date_plantation = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: "L'heure de crop est obligatoire.")]
    private ?\DateTimeInterface $heure_crop = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de crop est obligatoire.")]
    private ?\DateTimeInterface $date_crop = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: "L'heure de plantation est obligatoire.")]
    private ?\DateTimeInterface $heure_plantation = null;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank(message: "La température est obligatoire.")]
    #[Assert\Range(
        min: -50,
        max: 100,
        notInRangeMessage: "La température doit être comprise entre -50 et 100 degrés."
    )]
    private ?float $temperature = null;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank(message: "L'humidité est obligatoire.")]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: "L'humidité doit être comprise entre 0 et 100%."
    )]
    private ?float $humidite = null;

    #[ORM\OneToMany(targetEntity: Soildata::class, mappedBy: 'crop', cascade: ["persist", "remove"])]
    private Collection $Soildata;

    public function __construct()
    {
        $this->Soildata = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCropEvent(): ?string
    {
        return $this->crop_event;
    }

    public function setCropEvent(string $crop_event): self
    {
        $this->crop_event = $crop_event;
        return $this;
    }

    public function getTypeCrop(): ?string
    {
        return $this->type_crop;
    }

    public function setTypeCrop(string $type_crop): self
    {
        $this->type_crop = $type_crop;
        return $this;
    }

    public function getMethodeCrop(): ?string
    {
        return $this->methode_crop;
    }

    public function setMethodeCrop(string $methode_crop): self
    {
        $this->methode_crop = $methode_crop;
        return $this;
    }

    public function getDatePlantation(): ?\DateTimeInterface
    {
        return $this->date_plantation;
    }

    public function setDatePlantation(\DateTimeInterface $date_plantation): self
    {
        $this->date_plantation = $date_plantation;
        return $this;
    }

    public function getHeureCrop(): ?\DateTimeInterface
    {
        return $this->heure_crop;
    }

    public function setHeureCrop(\DateTimeInterface $heure_crop): self
    {
        $this->heure_crop = $heure_crop;
        return $this;
    }

    public function getDateCrop(): ?\DateTimeInterface
    {
        return $this->date_crop;
    }

    public function setDateCrop(\DateTimeInterface $date_crop): self
    {
        $this->date_crop = $date_crop;
        return $this;
    }

    public function getHeurePlantation(): ?\DateTimeInterface
    {
        return $this->heure_plantation;
    }

    public function setHeurePlantation(\DateTimeInterface $heure_plantation): self
    {
        $this->heure_plantation = $heure_plantation;
        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): self
    {
        $this->temperature = $temperature;
        return $this;
    }

    public function getHumidite(): ?float
    {
        return $this->humidite;
    }

    public function setHumidite(float $humidite): self
    {
        $this->humidite = $humidite;
        return $this;
    }

    public function getSoildata(): Collection
    {
        return $this->Soildata;
    }

    public function addSoildatum(Soildata $soildatum): self
    {
        if (!$this->Soildata->contains($soildatum)) {
            $this->Soildata->add($soildatum);
            $soildatum->setCrop($this);
        }
        return $this;
    }

    public function removeSoildatum(Soildata $soildatum): self
    {
        if ($this->Soildata->removeElement($soildatum)) {
            if ($soildatum->getCrop() === $this) {
                $soildatum->setCrop(null);
            }
        }
        return $this;
    }
}
