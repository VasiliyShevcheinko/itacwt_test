<?php

namespace App\Repository;

use App\Entity\TaxNumberFormat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaxNumberFormat>
 *
 * @method TaxNumberFormat|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxNumberFormat|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxNumberFormat[]    findAll()
 * @method TaxNumberFormat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxNumberFormatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxNumberFormat::class);
    }
}
