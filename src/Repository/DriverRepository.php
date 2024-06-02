<?php

namespace App\Repository;

use App\Entity\Driver;
use App\Entity\Group;
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

    public function getDriverPagination(){
        return $this->createQueryBuilder('d')
            ->select('d')
            ->addSelect('u')
            ->leftJoin('d.user', 'u')
            ->getQuery();
    }
    public function findAllDrivers(){
        return $this->createQueryBuilder('d')
            ->select('d')
            ->addSelect('u')
            ->leftJoin('d.user', 'u')
            ->getQuery()
            ->getResult();
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
    public function getDriverSchedules($driver)
    {
        $qb = $this->createQueryBuilder('d');

        return $qb->where('d.id = :id')
            ->setParameter('id', $driver->getId())
            ->leftJoin('d.schedules', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult();
    }

    public function getDriverAbsences($driver)
    {
        $qb = $this->createQueryBuilder('d');

        return $qb->where('d.id = :id')
            ->setParameter('id', $driver->getId())
            ->leftJoin('d.absences', 'a')
            ->addSelect('a')
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findDriversByGroup(Group $group): array
    {
        $qb = $this->createQueryBuilder('d')
            ->innerJoin('d.groupCollection', 'g')
            ->where('g = :group')
            ->setParameter('group', $group)
            ->getQuery();

        return $qb->getResult();
    }
    public function findDriversByGroupOrderedByDaysDriven(Group $group): array
    {
        $qb = $this->createQueryBuilder('d')
            ->innerJoin('d.groupCollection', 'g')
            ->where('g = :group')
            ->setParameter('group', $group)
            ->orderBy('d.daysDriven', 'ASC')
            ->getQuery();

        return $qb->getResult();
    }
    public function findDriversGroupedByScheduleOrderedByDaysDriven(Group $group): array
    {
        $qb = $this->createQueryBuilder('d')
            ->innerJoin('d.groupCollection', 'g')
            ->innerJoin('d.schedules', 's')
            ->addSelect('s') // Asegura que los schedules sean seleccionados
            ->where('g = :group')
            ->setParameter('group', $group)
            ->orderBy('s.weekDay', 'ASC')
            ->addOrderBy('s.entrySlot', 'ASC')
            ->addOrderBy('d.daysDriven', 'ASC')
            ->getQuery();

        return $qb->getResult();
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
        $this->getEntityManager()->flush();
    }

    public function add(Driver $driver){
        $this->getEntityManager()->persist($driver);
        $this->getEntityManager()->flush();
    }
}
