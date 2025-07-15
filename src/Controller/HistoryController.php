<?php

namespace App\Controller;

use App\Service\HistoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/history', name: 'app_history_', format: 'json')]
class HistoryController extends AbstractController
{
    public function __construct(private HistoryService $historyService) {}

    /**
     * Searches history entries by secret code
     */
    #[Route('/search', name: 'search', methods: ['GET'])]
    public function searchByCode(Request $request): JsonResponse
    {
        $code = $request->query->get('code');
        
        $histories = $this->historyService->searchByCode($code);
        
        return $this->json([
            'success' => true,
            'data' => $this->historyService->formatHistoryEntries($histories),
            'count' => count($histories)
        ]);
    }

    /**
     * Searches history entries by user ID
     */
    #[Route('/user/{id}', name: 'user', methods: ['GET'])]
    public function searchByUser(int $id): JsonResponse
    {
        $histories = $this->historyService->searchByUser($id);
        
        return $this->json([
            'success' => true,
            'data' => $this->historyService->formatHistoryEntries($histories),
            'count' => count($histories)
        ]);
    }
}