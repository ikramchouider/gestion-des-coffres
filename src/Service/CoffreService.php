<?php

namespace App\Service;

use App\Entity\Coffre;
use App\Entity\SecretCodeHistory;
use App\Entity\User;
use App\Exception\CoffreNotFoundException;
use App\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class CoffreService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SecretCodeGenerator $codeGenerator
    ) {}

    /**
     * Creates a new coffre (safe) with a unique secret code and owner
     * @param array $data Contains name and username of owner
     * @return Coffre The newly created coffre entity
     */
    public function createCoffre(array $data): Coffre
    {
        $coffre = new Coffre();
        $coffre->setName($data['name'] ?? 'New Coffre');
        
        $user = $this->getUserByUsername($data['username']);
        $coffre->setOwner($user);
        
        $uniqueCode = $this->codeGenerator->generateUniqueHexCode(36);
        $coffre->setCurrentSecretCode($uniqueCode);
        
        $this->createHistoryEntry($coffre, $uniqueCode, $user);
        
        $this->entityManager->persist($coffre);
        $this->entityManager->flush();

        return $coffre;
    }

    /**
     * Generates a new secret code for an existing coffre
     * @param array $data Contains coffre ID and username of requester
     * @return Coffre The updated coffre entity with new code
     */
    public function regenerateCode(array $data): Coffre
    {
        $coffreID = is_numeric($data['coffreId']) ? (int)$data['coffreId'] : $data['coffreId'];
        $coffre = $this->getAuthorizedCoffre($coffreID);
        $user = $this->getUserByUsername($data['username']);
        
        $newCode = $this->codeGenerator->generateUniqueHexCode(36);
        $coffre->setCurrentSecretCode($newCode);
        
        $this->createHistoryEntry($coffre, $newCode, $user);
        
        $this->entityManager->flush();

        return $coffre;
    }

    /**
     * Retrieves a user entity by their username/email
     * @param string $username The user's identifier
     * @return User The found user entity
     * @throws UserNotFoundException If user doesn't exist
     */
    private function getUserByUsername(string $username): User
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $username]);
        
        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        return $user;
    }

    /**
     * Retrieves a coffre by ID and verifies its existence
     * @param int $id The coffre's identifier
     * @return Coffre The found coffre entity
     * @throws CoffreNotFoundException If coffre doesn't exist
     */
    public function getAuthorizedCoffre(int $id): Coffre
    {
        $coffre = $this->entityManager->getRepository(Coffre::class)->find($id);
        
        if (!$coffre) {
            throw new CoffreNotFoundException();
        }

        return $coffre;
    }

    /**
     * Creates a history entry for secret code generation
     * @param Coffre $coffre The related coffre
     * @param string $code The generated secret code
     * @param User $user The user who generated the code
     */
    private function createHistoryEntry(Coffre $coffre, string $code, User $user): void
    {
        $history = new SecretCodeHistory();
        $history->setSecretCode($code);
        $history->setGeneratedBy($user);
        $history->setCoffre($coffre);
        $history->setGeneratedAt(new \DateTimeImmutable());
       
        $this->entityManager->persist($history);
        $coffre->addSecretCodeHistory($history);
    }

    public function getAllCoffres(): array
    {
        return $this->entityManager->getRepository(Coffre::class)->findAll();
    }
}