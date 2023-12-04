<?php

namespace App\Repository;

use App\Entity\Product;
use App\Infrastructure\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function findOrFail(int $id): Product
    {
        $entity = $this->find($id);

        if (!$entity instanceof Product) {
            throw new EntityNotFoundException(Product::class);
        }

        return $entity;
    }
}
