<?php

namespace App\Repository;

use App\Entity\SecretCodeHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SecretCodeHistory>
 */
class SecretCodeHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecretCodeHistory::class);
    }
        public function findByUserOrderedByDate(User $user): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.generatedBy = :user')
            ->setParameter('user', $user)
            ->orderBy('h.generatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCodeAndUser(string $code, User $user): array
    {
        return $this->createQueryBuilder('h')
            ->join('h.coffre', 'c')
            ->andWhere('h.secretCode = :code')
            ->andWhere('c.owner = :user')
            ->setParameter('code', $code)
            ->setParameter('user', $user)
            ->orderBy('h.generatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCoffreOrderedByDate(Coffre $coffre): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.coffre = :coffre')
            ->setParameter('coffre', $coffre)
            ->orderBy('h.generatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return SecretCodeHistory[] Returns an array of SecretCodeHistory objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SecretCodeHistory
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
