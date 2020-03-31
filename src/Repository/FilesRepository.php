<?php

namespace App\Repository;

use App\Entity\Files;
use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Files|null find($id, $lockMode = null, $lockVersion = null)
 * @method Files|null findOneBy(array $criteria, array $orderBy = null)
 * @method Files[]    findAll()
 * @method Files[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Files::class);
    }

    public function total (array $criteria) {
        $builder = $this->_em->createQueryBuilder();
        $builder
            ->select('COUNT(f.id)')
            ->from(Files::class, 'f')
        ;
        if ($criteria) {
            $criteria = Criteria::create();
            foreach ($criteria as $criterion => $key) {
                try {
                    $criteria->andWhere(Criteria::expr()->eq($key, $criterion));
                } catch (QueryException $e) {
                    return 0;
                }
            }
        }
        try {
            return $builder->getQuery()->getSingleScalarResult();
        } catch (Exception $e) {
            return 0;
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }
    // /**
    //  * @return Files[] Returns an array of Files objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Files
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
