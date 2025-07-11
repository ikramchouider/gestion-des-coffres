<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CoffreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/coffres', format: 'json')]
class CoffreController extends AbstractController
{
    public function __construct(
        private CoffreService $coffreService,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/create', name: 'coffre_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // For testing: Get the first user from database
        $user = $this->getTestUser();
        
        $coffre = $this->coffreService->createCoffre($data, $user);

        return $this->json([
            'id' => $coffre->getId(),
            'name' => $coffre->getName(),
            'secret_code' => $coffre->getCurrentSecretCode()
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}/regenerate-code', name: 'coffre_regenerate_code', methods: ['POST'])]
    public function regenerateCode(int $id): JsonResponse
    {
        // For testing: Get the first user from database
        $user = $this->getTestUser();
        
        $coffre = $this->coffreService->regenerateCode($id, $user);

        return $this->json([
            'new_secret_code' => $coffre->getCurrentSecretCode()
        ]);
    }

    #[Route('/{id}/history', name: 'coffre_history', methods: ['GET'])]
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

    /**
     * Temporary method to get a test user while JWT isn't working
     */
    private function getTestUser(): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        
        if (!$user) {
            // Create a test user if none exists
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword('testpassword');
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        
        return $user;
    }
}