<?php

namespace App\Entity;

use App\Repository\EpisodeRatingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpisodeRatingRepository::class)]
class EpisodeRating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $episode = null;

    #[ORM\Column]
    private ?float $sentinel_score = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

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

    public function getSentinelScore(): ?float
    {
        return $this->sentinel_score;
    }

    public function setSentinelScore(float $sentinel_score): static
    {
        $this->sentinel_score = $sentinel_score;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }
}
