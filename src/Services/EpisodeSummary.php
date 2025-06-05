<?php

namespace App\Services;

use App\Interfaces\EpisodeInterface;
use App\Interfaces\EpisodeReviewInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EpisodeSummary
{

    public function __construct(private EpisodeReviewInterface $episodeReview)
    {

    }

    public function getSummary(EpisodeInterface $episode): array
    {
        return [
            'name'=>$episode->getName(),
            'date'=>$episode->getAirDate()->format('Y-m-d'),
            'rating'=>$this->episodeReview->averageRating($episode),
            'last_reviews'=>$this->episodeReview->lastRates($episode)
        ];
    }

}
