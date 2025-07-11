<?php

namespace App\Service;

use App\Entity\Coffre;
use App\Repository\CoffreRepository;
use Doctrine\ORM\EntityManagerInterface;

class SecretCodeGenerator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private int $maxAttempts = 10
    ) {}
    
    /**
     * Generate a unique hexadecimal code 
     */

    public function generateUniqueHexCode(int $length): string
    {
        /** @var CoffreRepository $repository */
        $repository = $this->entityManager->getRepository(Coffre::class);
        $attempt = 0;
        
        do {
            $code = bin2hex(random_bytes($length / 2));
            
            $exists = $repository->codeExistsInCoffreOrHistory($code);
            
            if (!$exists) {
                return $code;
            }
            
            $attempt++;
        } while ($attempt < $this->maxAttempts);

        throw new \RuntimeException('Failed to generate unique code after '.$this->maxAttempts.' attempts');
    }
}