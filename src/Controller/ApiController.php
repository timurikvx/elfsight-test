<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Interfaces\EpisodeReviewInterface;
use App\Interfaces\EpisodeServiceInterface;
use App\Interfaces\EpisodeSummaryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Validation\EpisodeReviewValidation;

class ApiController
{

    #[Route('/api/episodes/import', name: 'episodes', methods: ['POST'])]
    public function episodes(EpisodeServiceInterface $episodeService): Response
    {
        $result = $episodeService->import();
        return new JsonResponse($result, 200);
    }

    #[Route('/api/episode/review', name: 'rating', methods: ['POST'])]
    public function rating(Request $request, EpisodeServiceInterface $episodeService, EpisodeReviewInterface $episodeReview, EpisodeReviewValidation $validation): Response
    {
        $data = json_decode($request->getContent(), true);
        $validation->validateRating($data);

        $episode = $episodeService->getEpisode($data['id'], Episode::class);
        $rating = $episodeReview->review($episode, $data['text']);
        return new JsonResponse(['rating'=>$rating], 200);
    }

    #[Route('/api/episode/review/{id}', name: 'ratingByID', methods: ['POST'])]
    public function ratingByID(string $id, Request $request, EpisodeServiceInterface $episodeService, EpisodeReviewInterface $episodeReview, EpisodeReviewValidation $validation): Response
    {
        $data = json_decode($request->getContent(), true);
        $data['id'] = intval($id);

        $validation->validateRating($data);

        $episode = $episodeService->getEpisode($data['id'], Episode::class);
        $rating = $episodeReview->review($episode, $data['text']);
        return new JsonResponse(['rating'=>$rating], 200);
    }

    #[Route('/api/episode/summary/{id}', name: 'summary', methods: ['POST'])]
    public function summary(string $id, EpisodeServiceInterface $episodeService, EpisodeSummaryInterface $episodeSummary, EpisodeReviewValidation $validation): Response
    {
        $value = intval($id);
        $validation->validateSummary($id);

        $episode = $episodeService->getEpisode($value, Episode::class);
        $data = $episodeSummary->getSummary($episode);
        return new JsonResponse($data, 200);
    }

    #[Route('/api/episodes/list', name: 'list', methods: ['POST'])]
    public function list(EpisodeServiceInterface $episodeService): Response
    {
        $data = $episodeService->list();
        return new JsonResponse($data, 200);
    }

}
