<?php

namespace App\Entity;

use App\Repository\AverageRatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AverageRatingRepository::class)]
class AverageRating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $episode = null;

    #[ORM\Column]
    private ?float $rate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEpisode(): ?int
    {
        return $this->episode;
    }

    public function setEpisode(int $episode): static
    {
        $this->episode = $episode;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }
}
