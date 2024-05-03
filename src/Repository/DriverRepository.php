<?php

namespace App\Repository;

use App\Entity\Driver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Driver>
 *
 * @method Driver|null find($id, $lockMode = null, $lockVersion = null)
 * @method Driver|null findOneBy(array $criteria, array $orderBy = null)
 * @method Driver[]    findAll()
 * @method Driver[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DriverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Driver::class);
    }

    public function findDriverById($id)
    {
        $qb = $this->createQueryBuilder('d');

        return $qb->select('d')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findDriverSchedule(int $id)
    {
        $qb = $this->createQueryBuilder('d');

        return $qb->where('d.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('d.schedules', 's')
            ->addSelect('s')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function save()
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Driver $driver){
        if ($driver->getUser()) {
            $this->getEntityManager()->remove($driver->getUser());
        }
        $this->getEntityManager()->remove($driver);
    }

    public function add(Driver $driver){
        $this->getEntityManager()->persist($driver);
    }
}
