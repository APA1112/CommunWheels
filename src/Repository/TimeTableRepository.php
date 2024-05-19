<?php

namespace App\Repository;

use App\Entity\TimeTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeTable>
 *
 * @method TimeTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeTable[]    findAll()
 * @method TimeTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeTable::class);
    }
    public function findByGroup($groupId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.band = :groupId')
            ->setParameter('groupId', $groupId)
            ->orderBy('t.weekStartDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findById($id){
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save()
    {
        $this->getEntityManager()->flush();
    }
}
