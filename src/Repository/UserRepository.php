<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    public function findGroupAdmins(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.isGroupAdmin = 1')
            ->getQuery()
            ->getResult()
            ;
    }
    public function save()
    {
        $this->getEntityManager()->flush();
    }

    public function remove(User $user){
        $this->getEntityManager()->remove($user);
    }

    public function add(User $user){
        $this->getEntityManager()->persist($user);
    }
}
