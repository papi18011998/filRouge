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
    $role = new Role();
    $role->setLibelle("SUPER_ADMIN");
    $manager->persist($role);
    $role1 = new Role();
    $role1->setLibelle("ADMIN");
    $manager->persist($role1);
    $role2 = new Role();
    $role2->setLibelle("CAISSIER");
    $manager->persist($role2);
    $role3 = new Role();
    $role3->setLibelle("PARTENAIRE");
    $manager->persist($role3);
    $user = new User();
    $user->setPassword($this->encoder->encodePassword($user, "passer@1"));
    $user->setPrenom("Papa Ibrahima")
         ->setNom("NDIAYE")
         ->setEmail("papaibrahima98@gmail.com")
         ->setRole($role)
         ->setIsActive(true);
    $manager->persist($user);
    $manager->flush();
    }
}