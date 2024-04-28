<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 *
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function groupsData()
    {
        $qb = $this->createQueryBuilder('g');
        return $qb
            ->leftJoin('g.drivers', 'd')
            ->getQuery()
            ->getResult();
    }

    public function findGroupById(int $id)
    {
        $qb = $this->createQueryBuilder('g');
        /*Preguntar para un join de tres tablas*/
        return $qb
            ->leftJoin('g.drivers', 'd')
            ->andWhere('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findGroupsByDriverId($driverId)
    {
        $qb = $this->createQueryBuilder('g');
        $qb->innerJoin('g.drivers', 'd')
            ->andWhere('d.id = :driverId')
            ->setParameter('driverId', $driverId);

        return $qb->getQuery()->getResult();
    }

    public function save()
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Group $group){
        $this->getEntityManager()->remove($group);
    }

    public function add(Group $group){
        $this->getEntityManager()->persist($group);
    }
}
