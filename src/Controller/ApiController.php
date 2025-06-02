<?php

namespace App\Controller;

use App\Services\EpisodeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Validation\EpisodeRatingValidation;

class ApiController
{

    #[Route('/api/episodes/import', name: 'episodes', methods: ['POST'])]
    public function episodes(EpisodeService $episodeService): Response
    {
        $result = $episodeService->import();
        return new JsonResponse($result, 200);
    }

    #[Route('/api/episode/review', name: 'rating', methods: ['POST'])]
    public function rating(Request $request, EpisodeService $episodeService, EpisodeRatingValidation $validation): Response
    {
        $data = json_decode($request->getContent(), true);
        $validation->validateRating($data);
        $rating = $episodeService->rating($data['id'], $data['text']);
        return new JsonResponse(['rating'=>$rating], 200);
    }

    #[Route('/api/episode/review/{id}', name: 'ratingByID', methods: ['POST'])]
    public function ratingByID(string $id, Request $request, EpisodeService $episodeService, EpisodeRatingValidation $validation): Response
    {
        $data = json_decode($request->getContent(), true);
        $data['id'] = intval($id);

        $validation->validateRating($data);
        $rating = $episodeService->rating($data['id'], $data['text']);
        return new JsonResponse(['rating'=>$rating], 200);
    }

    #[Route('/api/episode/summary/{id}', name: 'summary', methods: ['POST'])]
    public function summary(string $id, EpisodeService $episodeService, EpisodeRatingValidation $validation): Response
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
