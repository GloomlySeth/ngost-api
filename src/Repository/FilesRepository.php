<?php

namespace App\Repository;

use App\Entity\Files;
use App\Entity\News;
use App\Entity\UserRequest;
use App\Entity\Users;
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

    /**
     * @param Users $user
     * @param $sort
     * @param $limit
     * @param $offset
     * @param null $search
     * @return Files[]
     */
    public function filterBy (Users $user, $sort, $limit, $offset,$search = null) {
        $builder = $this->_em->createQueryBuilder('qf');
        $builder->select('f');
        $builder->from('App:Files', 'f');
        if ($user) {
            $builder->andWhere('f.user = :user');
            $builder->setParameter('user',$user);
        }
        if ($search['search']) {
            $builder->andWhere('f.title LIKE :search');
            $builder->setParameter('search', '%'.$search['search'].'%');
        }

        $builder->leftJoin(UserRequest::class, 'r', 'WITH', 'r.id = f.request');
        if ($search['filter']) {
            if ($search['filter'] !== 'process') {
                $builder->andWhere('r.status = :filter');
                $builder->setParameter('filter', $search['filter']);
            } else {
                $builder->andWhere('r.status > 0');
                $builder->andWhere('r.status < 101');
            }
        }
        if (key($sort) == 'request.status') {
            $builder->orderBy('r.status', $sort[key($sort)]);
        } else if (key($sort) == 'request.requirement') {
            $builder->orderBy('r.requirement', $sort[key($sort)]);
        } else if ($sort) {
            $builder->orderBy('f.'.key($sort), $sort[key($sort)]);
        }
        if ($limit > 0) {
            $builder->setMaxResults($limit);
            if ($offset > 0) {
                $builder->setFirstResult($offset - 1);
            } else {
                $builder->setFirstResult(0);
            }
        }
        return $builder->getQuery()->getResult();
    }

    public function total (Users $user, $search = null) {
        $builder = $this->_em->createQueryBuilder();
        $builder
            ->select('COUNT(f.id)')
            ->from(Files::class, 'f')
        ;
        if ($search['search']) {
            $builder->andWhere('f.title LIKE :search');
            $builder->setParameter('search', '%'.$search['search'].'%');
        }
        if ($user) {
            $builder->andWhere('f.user = :user');
            $builder->setParameter('user',$user);
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
