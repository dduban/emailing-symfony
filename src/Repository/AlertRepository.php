<?php

namespace App\Repository;

use App\Entity\Alert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Alert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alert[]    findAll()
 * @method Alert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlertRepository extends ServiceEntityRepository
{
    public function findOutOfRangeUsers(): array
    {
        return $this->createQueryBuilder('a')
            ->select('u.id as userId, u.email as userEmail')
            ->innerJoin('a.currency', 'c', Join::WITH, 'a.currency = c.id')
            ->innerJoin('a.id_user', 'u', Join::WITH, 'a.id_user = u.id')
            ->where('(a.min > c.value) OR (a.max < c.value)')
            ->groupBy('u.id')
            ->getQuery()
            ->execute();
    }

    public function findOutOfRange($user): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.currency', 'c', Join::WITH, 'a.currency = c.id')
            ->innerJoin('a.id_user', 'u', Join::WITH, 'a.id_user = u.id')
            ->where('((a.min > c.value) OR (a.max < c.value)) AND (u.id = :userId)')
            ->setParameter('userId', $user)
            ->getQuery()
            ->execute();
    }


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    // /**
    //  * @return Alert[] Returns an array of Alert objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Alert
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
