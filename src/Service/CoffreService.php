<?php

namespace App\Service;

use App\Entity\Coffre;
use App\Entity\SecretCodeHistory;
use App\Entity\User;
use App\Exception\CoffreNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class CoffreService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SecretCodeGenerator $codeGenerator
    ) {}
     /**
     * Handles coffre creations 
     */

    public function createCoffre(array $data, User $user): Coffre
    {
        $coffre = new Coffre();
        $coffre->setName($data['name'] ?? 'New Coffre');
        $coffre->setOwner($user);
        
        $uniqueCode = $this->codeGenerator->generateUniqueHexCode(36);
        $coffre->setCurrentSecretCode($uniqueCode);
        
        $this->createHistoryEntry($coffre, $uniqueCode, $user);
        
        $this->entityManager->persist($coffre);
        $this->entityManager->flush();

        return $coffre;
    }

    /**
     * Handles code regeneration 
     */

    public function regenerateCode(int $coffreId, User $user): Coffre
    {
        $coffre = $this->getAuthorizedCoffre($coffreId);
        
        $newCode = $this->codeGenerator->generateUniqueHexCode(36);
        $coffre->setCurrentSecretCode($newCode);
        
        $this->createHistoryEntry($coffre, $newCode, $user);
        
        $this->entityManager->flush();

        return $coffre;
    }
    /**
     * Handles coffre existing verification
     */

    private function getAuthorizedCoffre(int $id): Coffre
    {
        $coffre = $this->entityManager->getRepository(Coffre::class)->find($id);
        
        if (!$coffre) {
            throw new CoffreNotFoundException();
        }

        return $coffre;
    }
    /**
     * Handles history creation 
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
}