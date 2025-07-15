<?php

namespace App\Repository;

use App\Entity\Coffre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coffre>
 */
class CoffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coffre::class);
    }

    public function codeExistsInCoffreOrHistory(string $code): bool
    {
        // Get the EntityManager properly
        $em = $this->getEntityManager();

        // Check current codes
        $currentCodeExists = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.currentSecretCode = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleScalarResult();

        if ($currentCodeExists > 0) {
            return true;
        }

        // Check historical codes
        $historyCodeExists = $em->createQueryBuilder()
            ->select('COUNT(h.id)')
            ->from('App\Entity\SecretCodeHistory', 'h')
            ->where('h.secretCode = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleScalarResult();

        return $historyCodeExists > 0;
    }
}