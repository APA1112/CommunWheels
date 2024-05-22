<?php

namespace App\Repository;

use App\Entity\Group;
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

    public function findByGroup(Group $band): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.band = :band')
            ->setParameter('band', $band)
            ->orderBy('n.dayDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function save()
    {
        $this->getEntityManager()->flush();
    }
    public function remove(NonSchoolDay $nonSchoolDay){
        $this->getEntityManager()->remove($nonSchoolDay);
        $this->getEntityManager()->flush();
    }

    public function add(NonSchoolDay $nonSchoolDay){
        $this->getEntityManager()->persist($nonSchoolDay);
    }

}
