<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Services\EpisodeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Validation\EpisodeRateValidation;

class ApiController
{
    #[Route('/api/episodes/import', name: 'episodes', methods: ['POST'])]
    public function episodes(EpisodeService $episodeService): Response
    {
        $result = $episodeService->import();
        return new JsonResponse($result, 200);
    }

    #[Route('/api/episode/review', name: 'rate', methods: ['POST'])]
    public function rate(Request $request, EpisodeService $episodeService, EpisodeRateValidation $validation): Response
    {
        $data = json_decode($request->getContent(), true);
        $validation->validateRate($data);

        $episodeService->rate($data['id'], $data['text']);
        return new JsonResponse(['method'=>'rate'], 200);
    }

    #[Route('/api/episode/review/{id}', name: 'rateByID', methods: ['POST'])]
    public function rateByID(string $id, Request $request, EpisodeService $episodeService, EpisodeRateValidation $validation): Response
    {
        $data = json_decode($request->getContent(), true);
        $data['id'] = intval($id);

        $validation->validateRate($data);
        $rate = $episodeService->rate($data['id'], $data['text']);
        return new JsonResponse(['rate'=>$rate], 200);
    }

    #[Route('/api/episode/summary/{id}', name: 'summary', methods: ['POST'])]
    public function summary(string $id, EpisodeService $episodeService, EpisodeRateValidation $validation): Response
    {
        $value = intval($id);
        $validation->validateSummary($id);
        $data = $episodeService->getSummary($value);
        return new JsonResponse($data, 200);
    }

    #[Route('/api/episodes/list', name: 'list', methods: ['POST'])]
    public function list(EpisodeService $episodeService): Response
    {
        $data = $episodeService->list();
        return new JsonResponse($data, 200);
    }

}
