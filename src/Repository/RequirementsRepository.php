<?php

namespace App\Repository;

use App\Entity\Requirements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Requirements|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requirements|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requirements[]    findAll()
 * @method Requirements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequirementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requirements::class);
    }

    // /**
    //  * @return Requirements[] Returns an array of Requirements objects
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
    public function findOneBySomeField($value): ?Requirements
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
