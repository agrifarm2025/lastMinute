<?php

namespace App\Entity;

use App\Repository\SoildataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SoildataRepository::class)]
class Soildata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $niveau_ph = null;

    #[ORM\Column]
    private ?float $humidite = null;

    #[ORM\Column]
    private ?float $niveau_nutriment = null;

    #[ORM\Column(length: 30)]
    private ?string $type_sol = null;

    #[ORM\ManyToOne(inversedBy: 'soildata')]
    private ?Crop $crop = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNiveauPh(): ?float
    {
        return $this->niveau_ph;
    }

    public function setNiveauPh(float $niveau_ph): static
    {
        $this->niveau_ph = $niveau_ph;

        return $this;
    }

    public function getHumidite(): ?float
    {
        return $this->humidite;
    }

    public function setHumidite(float $humidite): static
    {
        $this->humidite = $humidite;

        return $this;
    }

    public function getNiveauNutriment(): ?float
    {
        return $this->niveau_nutriment;
    }

    public function setNiveauNutriment(float $niveau_nutriment): static
    {
        $this->niveau_nutriment = $niveau_nutriment;

        return $this;
    }

    public function getTypeSol(): ?string
    {
        return $this->type_sol;
    }

    public function setTypeSol(string $type_sol): static
    {
        $this->type_sol = $type_sol;

        return $this;
    }

    public function getCrop(): ?Crop
    {
        return $this->crop;
    }

    public function setCrop(?Crop $crop): static
    {
        $this->crop = $crop;

        return $this;
    }
}
