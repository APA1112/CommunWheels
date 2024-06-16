<?php

namespace App\Repository;

use App\Entity\Absence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Absence>
 *
 * @method Absence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Absence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Absence[]    findAll()
 * @method Absence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbsenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Absence::class);
    }
    public function absencesPagination()
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->getQuery();
    }
    public function findAllAbsences()
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->getQuery()
            ->getResult();
    }
    public function driverAbsencesPagination($id){
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.driver = :id')
            ->setParameter('id', $id)
            ->orderBy('a.absenceDate', 'DESC')
            ->getQuery();
    }
    public function findDriverAbsences($id){
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.driver = :id')
            ->setParameter('id', $id)
            ->orderBy('a.absenceDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function save()
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Absence $absence){
        $this->getEntityManager()->remove($absence);
    }

    public function add(Absence $absence){
        $this->getEntityManager()->persist($absence);
    }
}
