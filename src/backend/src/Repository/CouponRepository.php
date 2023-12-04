<?php

namespace App\Repository;

use App\Entity\Coupon;
use App\Infrastructure\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coupon>
 *
 * @method Coupon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coupon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coupon[]    findAll()
 * @method Coupon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function findByCodeOrFail(string $code): Coupon
    {
        $entity = $this->findOneBy(['code' => $code]);

        if (!$entity instanceof Coupon) {
            throw new EntityNotFoundException(Coupon::class);
        }

        return $entity;
    }
}
