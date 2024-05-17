<?php

namespace App\Repository;

use App\Entity\Driver;
use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Schedule>
 *
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }
    public function findDriverSchedules(Driver $driver){
        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.driver = :driver')
            ->setParameter('driver', $driver)
            ->getQuery()
            ->getResult();
    }

    public function save()
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Schedule $schedule){
        $this->getEntityManager()->remove($schedule);
    }

    public function add(Schedule $schedule){
        $this->getEntityManager()->persist($schedule);
    }
}
