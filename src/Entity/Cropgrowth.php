<?php

namespace App\Entity;

use App\Repository\CropgrowthRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CropgrowthRepository::class)]
class Cropgrowth
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 100)]
    private ?string $observations = null;

    /**
     * @var Collection<int, Stages>
     */
    #[ORM\OneToMany(targetEntity: Stages::class, mappedBy: 'cropgrowth')]
    private Collection $stages;

    public function __construct()
    {

        {
            $this->stages = new ArrayCollection();
            // Predefine the 4 stages
            $this->addStage(new Stages('Plantation'));
            $this->addStage(new Stages('Croissance'));
            $this->addStage(new Stages('Maturité'));
            $this->addStage(new Stages('Récolte'));
        }    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStages(): ArrayCollection
    {
        return $this->stages;
    }

    public function setStages(array $stages): static
    {
        $this->stages = $stages;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(string $observations): static
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * @return Collection<int, Stages>
     */
    public function getStage(): Collection
    {
        return $this->stages;
    }

    public function addStage(Stages $stage): static
    {
        if (!$this->stages->contains($stage)) {
            $this->stages->add($stage);
            $stage->setCropgrowth($this);
        }

        return $this;
    }

    public function removeStage(Stages $stage): static
    {
        if ($this->stages->removeElement($stage)) {
            // set the owning side to null (unless already changed)
            if ($stage->getCropgrowth() === $this) {
                $stage->setCropgrowth(null);
            }
        }

        return $this;
    }
}
