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
     * Generates a cryptographically secure hexadecimal code that's unique across all coffres
     * and their history. Retries up to maxAttempts times before throwing an exception.
     * 
     * @param int $length The desired length of the hexadecimal code (must be even number)
     * @return string The generated unique hexadecimal code
     * @throws \RuntimeException When unable to generate a unique code after maxAttempts
     */
    public function generateUniqueHexCode(int $length): string
    {
        /** @var CoffreRepository $repository */
        $repository = $this->entityManager->getRepository(Coffre::class);
        $attempt = 0;
        
        do {
            // Generate cryptographically secure random bytes and convert to hex
            $code = bin2hex(random_bytes($length / 2));
            
            // Verify code doesn't exist in current codes or history
            $exists = $repository->codeExistsInCoffreOrHistory($code);
            
            if (!$exists) {
                return $code;
            }
            
            $attempt++;
        } while ($attempt < $this->maxAttempts);

        throw new \RuntimeException('Failed to generate unique code after '.$this->maxAttempts.' attempts');
    }
}