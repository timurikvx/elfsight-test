<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiController
{
    #[Route('/api/episodes', name: 'episodes', methods: ['POST'])]
    public function episodes(): Response
    {
        return new JsonResponse(['method'=>'episodes'], 200);
    }

    #[Route('/api/episode/rate', name: 'rate', methods: ['POST'])]
    public function rate(): Response
    {
        return new JsonResponse(['method'=>'rate'], 200);
    }

    #[Route('/api/episode/{id}/summary', name: 'summary', methods: ['POST'])]
    public function summary(): Response
    {
        return new JsonResponse(['method'=>'summary'], 200);
    }

}
