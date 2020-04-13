<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    // /**
    //  * @return Role[] Returns an array of Role objects
    //  */
    public function getAdminRoleView()
    {
        return $this->createQueryBuilder('r')
            ->orWhere('r.libelle = :val')
            ->orWhere('r.libelle = :val2')
            ->orWhere('r.libelle = :val3')
            ->setParameter('val', "ADMIN_PARTENAIRE")
            ->setParameter('val2',"CAISSIER")
            ->setParameter('val3',"USER_PARTENAIRE")
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    public function getAdminPartnerRoleView()
    {
        return $this->createQueryBuilder('r')
            ->orWhere('r.libelle = :val')
            ->setParameter('val', "USER_PARTENAIRE")
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAll(){
       return $this->createQueryBuilder('r')
                   ->andWhere('r.libelle != :val')
                   ->setParameter('val',"SUPER_ADMIN")
                   ->getQuery()
                   ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Role
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
