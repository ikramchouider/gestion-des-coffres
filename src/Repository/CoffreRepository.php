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
        $historyCodeExists = $this->_em->createQueryBuilder()
            ->select('COUNT(h.id)')
            ->from('App\Entity\SecretCodeHistory', 'h')
            ->where('h.secretCode = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getSingleScalarResult();

        return $historyCodeExists > 0;
    }

    //    /**
    //     * @return Coffre[] Returns an array of Coffre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Coffre
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
