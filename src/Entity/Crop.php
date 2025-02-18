<?php

namespace App\Entity;

use App\Repository\CropRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CropRepository::class)]
class Crop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $crop_event = null;

    #[ORM\Column(length: 30)]
    private ?string $type_crop = null;

    #[ORM\Column(length: 20)]
    private ?string $methode_crop = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_plantation = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure_crop = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_crop = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure_plantation = null;
    /**
     * @var Collection<int, Soildata>
     */
    #[ORM\OneToMany(targetEntity: Soildata::class, mappedBy: 'crop')]
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

    public function setCropEvent(string $crop_event): static
    {
        $this->crop_event = $crop_event;

        return $this;
    }

    public function getTypeCrop(): ?string
    {
        return $this->type_crop;
    }

    public function setTypeCrop(string $type_crop): static
    {
        $this->type_crop = $type_crop;

        return $this;
    }

    public function getMethodeCrop(): ?string
    {
        return $this->methode_crop;
    }

    public function setMethodeCrop(string $methode_crop): static
    {
        $this->methode_crop = $methode_crop;

        return $this;
    }

    public function getDatePlantation(): ?\DateTimeInterface
    {
        return $this->date_plantation;
    }

    public function setDatePlantation(\DateTimeInterface $date_plantation): static
    {
        $this->date_plantation = $date_plantation;

        return $this;
    }

    public function getHeureCrop(): ?\DateTimeInterface
    {
        return $this->heure_crop;
    }

    public function setHeureCrop(\DateTimeInterface $heure_crop): static
    {
        $this->heure_crop = $heure_crop;

        return $this;
    }

    public function getDateCrop(): ?\DateTimeInterface
    {
        return $this->date_crop;
    }

    public function setDateCrop(\DateTimeInterface $date_crop): static
    {
        $this->date_crop = $date_crop;

        return $this;
    }

    public function getHeurePlantation(): ?\DateTimeInterface
    {
        return $this->heure_plantation;
    }

    public function setHeurePlantation(\DateTimeInterface $heure_plantation): static
    {
        $this->heure_plantation = $heure_plantation;

        return $this;
    }


    /**
     * @return Collection<int, Soildata>
     */
    public function getSoildata(): Collection
    {
        return $this->Soildata;
    }

    public function addSoildatum(Soildata $soildatum): static
    {
        if (!$this->Soildata->contains($soildatum)) {
            $this->Soildata->add($soildatum);
            $soildatum->setCrop($this);
        }

        return $this;
    }

    public function removeSoildatum(Soildata $soildatum): static
    {
        if ($this->Soildata->removeElement($soildatum)) {
            // set the owning side to null (unless already changed)
            if ($soildatum->getCrop() === $this) {
                $soildatum->setCrop(null);
            }
        }

        return $this;
    }

}
