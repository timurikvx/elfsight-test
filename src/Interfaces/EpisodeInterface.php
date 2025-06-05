<?php

namespace App\Interfaces;

interface EpisodeInterface
{
    public function getId(): ?int;

    public function setId(int $id): static;

    public function getName(): ?string;

    public function setName(string $name): static;

    public function getAirDate(): ?\DateTime;

    public function setAirDate(\DateTime $air_date): static;

    public function getEpisode(): ?string;

    public function setEpisode(string $episode): static;

    public function getApiId(): ?int;

    public function setApiId(int $api_id);
}
