<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CoffreService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/coffres', name: 'app_coffre_', format: 'json')]
class CoffreController extends AbstractController
{
    public function __construct(
        private CoffreService $coffreService
    ) {}

    /**
     * Creates a new coffre (safe) with the provided data
     */
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $coffre = $this->coffreService->createCoffre($data);

        return $this->json([
            'id' => $coffre->getId(),
            'name' => $coffre->getName(),
            'secret_code' => $coffre->getCurrentSecretCode()
        ], Response::HTTP_CREATED);
    }

    /**
     * Regenerates a new secret code for the specified coffre
     */
    #[Route('/{id}/regenerate-code', name: 'regenerate_code', methods: ['POST'])]
    public function regenerateCode(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $coffre = $this->coffreService->regenerateCode($data);

        return $this->json([
            'new_secret_code' => $coffre->getCurrentSecretCode()
        ]);
    }

    /**
     * Retrieves the history of secret codes for the specified coffre
     */
    #[Route('/{id}/history', name: 'history', methods: ['GET'])]
    public function getHistory(int $id): JsonResponse
    {
        $coffre = $this->coffreService->getAuthorizedCoffre($id);
        
        $history = array_map(function($entry) {
            return [
                'code' => $entry->getSecretCode(),
                'generated_by' => $entry->getGeneratedBy()->getEmail(),
                'generated_at' => $entry->getGeneratedAt()->format(\DateTimeInterface::ATOM)
            ];
        }, $coffre->getSecretCodeHistories()->toArray());

        return $this->json($history);
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(CoffreService $coffreService): JsonResponse
    {
        $coffres = $coffreService->getAllCoffres();
        
        return $this->json([
            'coffres' => array_map(function($coffre) {
                return [
                    'id' => $coffre->getId(),
                    'name' => $coffre->getName(),
                    'current_code' => $coffre->getCurrentSecretCode(),
                    'created_at' => $coffre->getCreatedAt()->format('Y-m-d H:i:s'),
                    'owner' => $coffre->getOwner()->getEmail(),
                ];
            }, $coffres)
        ]);
    }
}