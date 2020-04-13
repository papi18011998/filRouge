<?php

namespace App\Repository;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    // public function findAll(){
    //     return $this->createQueryBuilder('u')
    //         ->andWhere('u.nom != :val')
    //         ->setParameter('val',"NDIAYE")
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
    public function findAll(){
    $conn = $this->getEntityManager()->getConnection();
    $sql = '
        SELECT u.id,u.prenom,u.nom,u.is_active,u.email,r.libelle FROM user u,role r
        WHERE r.id = u.role_id
        AND r.libelle != :libelle
        ';
    $stmt = $conn->prepare($sql);
    $stmt->execute(['libelle' => "SUPER_ADMIN"]);
    return $stmt->fetchAll();
}
public function getAdminView(){
    $conn = $this->getEntityManager()->getConnection();
    $sql = '
        SELECT u.id,u.prenom,u.nom,u.is_active,u.email,r.libelle FROM user u,role r
        WHERE r.id = u.role_id
        AND r.libelle != :libelle
        AND r.libelle != :libelle1
        ';
    $stmt = $conn->prepare($sql);
    $stmt->execute(['libelle' => "ADMIN",'libelle1'=>"SUPER_ADMIN"]);
    return $stmt->fetchAll();
}
public function getAdminPartnerView(){
    $conn = $this->getEntityManager()->getConnection();
    $sql = '
        SELECT u.id,u.prenom,u.nom,u.is_active,u.email,r.libelle FROM user u,role r
        WHERE r.id = u.role_id
        AND r.libelle != :libelle
        AND r.libelle != :libelle1
        AND r.libelle != :libelle2
        AND r.libelle != :libelle3
        ';
    $stmt = $conn->prepare($sql);
    $stmt->execute(['libelle' => "ADMIN",'libelle1'=>"SUPER_ADMIN",'libelle2'=>"CAISSIER",'libelle3'=>"ADMIN_PARTENAIRE"]);
    return $stmt->fetchAll();
}
}
