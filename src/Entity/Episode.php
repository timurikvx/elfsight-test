<?php

namespace App\Entity;

use App\Repository\EpisodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Interfaces\EpisodeInterface;

#[ORM\Entity(repositoryClass: EpisodeRepository::class)]
class Episode implements EpisodeInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $air_date = null;

    #[ORM\Column(length: 255)]
    private ?string $episode = null;

    #[ORM\Column]
    private ?\DateTime $created = null;

    #[ORM\Column(name: 'api_id', type: 'integer', unique: true)]
    private ?int $api_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getAirDate(): ?\DateTime
    {
        return $this->air_date;
    }

    public function setAirDate(\DateTime $air_date): static
    {
        $this->air_date = $air_date;

        return $this;
    }

    public function getEpisode(): ?string
    {
        return $this->episode;
    }

    public function setEpisode(string $episode): static
    {
        $this->episode = $episode;

        return $this;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getApiId(): ?int
    {
        return $this->api_id;
    }

    public function setApiId(int $api_id): static
    {
        $this->api_id = $api_id;

        return $this;
    }
}
