<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager){
    // création du role superAdmin
    $role = new Role();
    $role->setLibelle("SUPER_ADMIN");
    $manager->persist($role);
    // création du role admin
    $role1 = new Role();
    $role1->setLibelle("ADMIN");
    $manager->persist($role1);
    // création du role caissier
    $role2 = new Role();
    $role2->setLibelle("CAISSIER");
    $manager->persist($role2);
    // création du role partenaire
    $role3 = new Role();
    $role3->setLibelle("PARTENAIRE");
    $manager->persist($role3);
    // création du user superAdmin
    $user = new User();
    $user->setPassword($this->encoder->encodePassword($user, "passer@1"));
    $user->setPrenom("Papa Ibrahima")
         ->setNom("NDIAYE")
         ->setEmail("papaibrahima98@gmail.com")
         ->setRole($role)
         ->setIsActive(true);
    $manager->persist($user);
    // création du user admin
    $user1 = new User();
    $user1->setPassword($this->encoder->encodePassword($user1, "passer@1"));
    $user1->setPrenom("Elhadji Ousmane")
         ->setNom("NDIAYE")
         ->setEmail("elzo@gmail.com")
         ->setRole($role1)
         ->setIsActive(true);
    $manager->persist($user1);
    // création du user caissier
    $user2 = new User();
    $user2->setPassword($this->encoder->encodePassword($user2, "passer@1"));
    $user2->setPrenom("djiby")
         ->setNom("NDIAYE")
         ->setEmail("djiby@gmail.com")
         ->setRole($role2)
         ->setIsActive(true);
    $manager->persist($user2);
    $manager->flush();
    }
}