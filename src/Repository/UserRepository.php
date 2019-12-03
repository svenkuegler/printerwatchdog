<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
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

    /**
     * Get all EMail-Addresses in a simple array
     * @return array
     */
    public function getAllEMailAddresses()
    {
        $emailArr = [];
        $result = $this->createQueryBuilder('email')
            ->andWhere('email.isActive = true')
            ->getQuery()
            ->getResult();
        foreach ($result as $user) {
            $emailArr[] = $user->getEmail();
        }

        return $emailArr;
    }

    /**
     * Get all Username in a simple array
     * @return array
     */
    public function getAllUsername()
    {
        $arr = [];
        $result = $this->createQueryBuilder('u')
            ->getQuery()
            ->getResult();
        foreach ($result as $user) {
            $arr[] = $user->getUsername();
        }

        return $arr;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
