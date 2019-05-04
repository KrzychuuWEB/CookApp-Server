<?php

namespace App\Repository;

use App\Entity\RecipePhotos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RecipePhotos|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipePhotos|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipePhotos[]    findAll()
 * @method RecipePhotos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipePhotosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RecipePhotos::class);
    }

    // /**
    //  * @return RecipePhotos[] Returns an array of RecipePhotos objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RecipePhotos
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
