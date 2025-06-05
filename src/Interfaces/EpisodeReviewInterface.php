<?php

namespace App\Interfaces;

interface EpisodeReviewInterface
{
    public function calculateAverage(EpisodeInterface $episode): void;

    function review(EpisodeInterface $episode, string $text): float;

    function averageRating(EpisodeInterface $episode): float;

    function lastRates(EpisodeInterface $episode): array;

}
