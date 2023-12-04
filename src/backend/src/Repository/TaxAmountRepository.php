<?php

namespace App\Repository;

use App\Entity\TaxAmount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaxAmount>
 *
 * @method TaxAmount|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxAmount|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxAmount[]    findAll()
 * @method TaxAmount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxAmountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxAmount::class);
    }

    //    /**
    //     * @return TaxAmount[] Returns an array of TaxAmount objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TaxAmount
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
