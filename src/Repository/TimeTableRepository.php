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
    public function findByGroup($group): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.trips', 'tr')
            ->addSelect('tr')
            ->where('t.band = :group')
            ->setParameter('group', $group)
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
    public function add(TimeTable $timeTable){
        $this->getEntityManager()->persist($timeTable);
        $this->getEntityManager()->flush();
    }
    public function remove(TimeTable $timeTable){
        $entityManager = $this->getEntityManager();

        // Verificar y eliminar los trips asociados, si existen
        $trips = $timeTable->getTrips();
        if ($trips) {
            foreach ($trips as $trip) {
                $entityManager->remove($trip);
            }
        }

        // Eliminar el TimeTable
        $entityManager->remove($timeTable);
        $entityManager->flush();
    }
}
