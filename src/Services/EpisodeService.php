<?php

namespace App\Services;

use App\Entity\AverageRating;
use App\Entity\Episode;
use App\Entity\EpisodeRating;
use Doctrine\Common\Collections\ArrayCollection;
use Sentiment\Analyzer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class EpisodeService
{

    private CacheInterface $cache;

    public function __construct(private HttpClientInterface $client, private EntityManagerInterface $entityManager, CacheInterface $cache)
    {
        $this->cache = $cache;
    }


    public function list(): array
    {
        return $this->cache->get('episodes_all', function (){
            return $this->listFromBase();
        });
    }

    private function listFromBase(): array
    {
        $q = 'SELECT t.api_id as ID, t.name, t.air_date as date, t.episode, COALESCE(r.rate, 0) as avg_rate FROM \App\Entity\Episode t LEFT JOIN \App\Entity\AverageRating as r WITH t.id = r.episode ORDER BY ID';
        $query = $this->entityManager->createQuery($q);
        $result = $query->execute();
        $collection = new ArrayCollection($result);
        $collection = $collection->map(function ($item){
            return [
                'ID'=>$item['ID'],
                'name'=>$item['name'],
                'date'=>$item['date']->format('Y-m-d'),
                'episode'=>$item['episode'],
                'avg_rating'=>floatval($item['avg_rate'])
            ];
        });
        return $collection->toArray();
    }

    public function import(): array
    {
        $id_list = $this->getAllId();

        $page = 1;
        $code = 200;
        $updated = 0;
        while ($code === 200){
            $response = $this->client->request('GET', 'https://rickandmortyapi.com/api/episode', [
                'query' => [
                    'page' => $page,
                ],
                'timeout'=>3000
            ]);
            try {
                $code = $response->getStatusCode();
            }catch (\Throwable $throwable){
                $code = 0;
            };
            if($code !== 200){
                break;
            }
            $data = json_decode($response->getContent(), true);
            foreach ($data['results'] as $item){
                if(in_array($item['id'], $id_list)){
                    continue;
                }
                $episode = new Episode();
                $episode->setApiId($item['id']);
                $episode->setAirDate((new \DateTime($item['air_date'])));
                $episode->setName($item['name']);
                $episode->setEpisode($item['episode']);
                $episode->setCreated((new \DateTime($item['created'])));
                $this->entityManager->persist($episode);
                $this->entityManager->flush();
                $updated += 1;
            }
            $page += 1;
        }
        if($updated > 0){
            $this->cache->delete('episodes_all');
        }
        return ['all'=>count($id_list), 'updated'=>$updated];

    }

    private function getAllId(): array
    {
        $all = $this->listFromBase();
        $collection = new ArrayCollection($all);
        return $collection->map(fn ($item) => $item['ID'])->toArray();
    }

    private function calculateAverage(Episode $episode): void
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

    public function rating($id, $text): float
    {
        $episode = $this->getEpisode($id);

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

    public function averageRating($id): float
    {
        return $this->cache->get('average_rank_episode_'.$id, function () use ($id){
            $averageRating = $this->entityManager->getRepository(AverageRating::class)->findOneBy(['episode'=> $id]);
            if(is_null($averageRating)){
                return 0;
            }
            return $averageRating->getRate();
        });
    }

    public function lastRates($id): array
    {
        return $this->cache->get('last_reviews_episode_'.$id, function () use ($id){
            $reviews = $this->entityManager->getRepository(EpisodeRating::class)->findBy(['episode'=> $id], ['id'=>'DESC'], 3);
            return (new ArrayCollection($reviews))->map(fn($item) => $item->getText())->toArray();
        });
    }

    private function getEpisode($id)
    {
        return $this->cache->get('episode_'.$id, function () use ($id){
            return $this->entityManager->getRepository(Episode::class)->findOneBy(['api_id'=> $id]);
        });
    }

    public function getSummary($id): array
    {
        $episode = $this->getEpisode($id);

        return [
            'name'=>$episode->getName(),
            'date'=>$episode->getAirDate()->format('Y-m-d'),
            'rating'=>$this->averageRating($episode->getId()),
            'last_reviews'=>$this->lastRates($episode->getId())
        ];
    }

}
