<?php

namespace App\Services;

use App\Entity\AverageRating;
use App\Entity\EpisodeRating;
use App\Interfaces\EpisodeInterface;
use App\Interfaces\EpisodeReviewInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sentiment\Analyzer;
use Symfony\Contracts\Cache\CacheInterface;

class EpisodeReview implements EpisodeReviewInterface
{
    public function __construct(private EntityManagerInterface $entityManager, CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function calculateAverage(EpisodeInterface $episode): void
    {
        $result = $this->entityManager->createQuery('SELECT AVG(t.sentinel_score) FROM App\Entity\EpisodeRating t WHERE t.episode = :episode')
            ->setParameter('episode', $episode->getId())->getSingleScalarResult();
        $average = round(floatval($result), 2);

        //Remove old value
        $this->entityManager->createQuery('DELETE FROM App\Entity\AverageRating t WHERE t.episode = :episode')
            ->setParameter('episode', $episode->getId())->execute();

        $rating = new AverageRating();
        $rating->setEpisode($episode->getId());
        $rating->setRate($average);
        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        $this->cache->delete('average_rank_episode_'.$episode->getId());
        $this->cache->delete('last_reviews_episode_'.$episode->getId());
    }

    public function review(EpisodeInterface $episode, string $text): float
    {
        $analyzer = new Analyzer();
        $result = $analyzer->getSentiment($text);
        $value = max(min($result['compound'], 1), 0);

        $rating = new EpisodeRating();
        $rating->setEpisode($episode->getId());
        $rating->setText($text);
        $rating->setSentinelScore($value);
        $this->entityManager->persist($rating);
        $this->entityManager->flush();
        $this->calculateAverage($episode);
        return $value;
    }

    public function averageRating(EpisodeInterface $episode): float
    {
        return $this->cache->get('average_rank_episode_'.$episode->getId(), function () use ($episode){
            $averageRating = $this->entityManager->getRepository(AverageRating::class)->findOneBy(['episode'=> $episode->getId()]);
            if(is_null($averageRating)){
                return 0;
            }
            return $averageRating->getRate();
        });
    }

    public function lastRates(EpisodeInterface $episode): array
    {
        return $this->cache->get('last_reviews_episode_'.$episode->getId(), function () use ($episode){
            $reviews = $this->entityManager->getRepository(EpisodeRating::class)->findBy(['episode'=> $episode->getId()], ['id'=>'DESC'], 3);
            return (new ArrayCollection($reviews))->map(fn($item) => $item->getText())->toArray();
        });
    }
}
