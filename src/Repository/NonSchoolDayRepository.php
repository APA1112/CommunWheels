<?php

namespace App\Repository;

use App\Entity\NonSchoolDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NonSchoolDay>
 *
 * @method NonSchoolDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method NonSchoolDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method NonSchoolDay[]    findAll()
 * @method NonSchoolDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NonSchoolDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NonSchoolDay::class);
    }

//    /**
//     * @return NonSchoolDay[] Returns an array of NonSchoolDay objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NonSchoolDay
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
