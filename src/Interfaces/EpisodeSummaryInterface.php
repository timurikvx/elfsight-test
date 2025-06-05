<?php

namespace App\Interfaces;

interface EpisodeSummaryInterface
{
    function getSummary(EpisodeInterface $episode): array;
}
