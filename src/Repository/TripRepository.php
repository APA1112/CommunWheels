<?php

namespace App\Repository;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 *
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function findByTimeTable($timeTableId){
        return $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.timeTable = :timeTableId')
            ->setParameter('timeTableId', $timeTableId)
            ->getQuery()
            ->getResult();
    }
    public function save()
    {
        $this->getEntityManager()->flush();
    }

    public function add(Trip $trip){
        $this->getEntityManager()->persist($trip);
        $this->getEntityManager()->flush();
    }
}
