<?php

namespace App\Interfaces;

interface EpisodeServiceInterface
{
    public function list(): array;

    public function import(): array;

    public function getEpisode(int $id, string $factory): EpisodeInterface;

}
