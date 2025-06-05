<?php

namespace App\Services;

use App\Entity\Episode;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use App\Interfaces\EpisodeInterface;
use App\Interfaces\EpisodeServiceInterface;

class EpisodeService implements EpisodeServiceInterface
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

    public function getEpisode(int $id, string $factory): EpisodeInterface
    {
        return $this->cache->get('episode_'.$id, function () use ($id, $factory){
            return $this->entityManager->getRepository($factory)->findOneBy(['api_id'=> $id]);
        });
    }

}
