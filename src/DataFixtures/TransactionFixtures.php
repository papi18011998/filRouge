<?php

namespace App\DataFixtures;

use App\Entity\Tarifs;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TransactionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tarif1 = new Tarifs();
        $tarif1->setBorneMin(1);
        $tarif1->setBorneMax(500);
        $tarif1->setCout(50);
        $manager->persist($tarif1);
        $tarif2 = new Tarifs();
        $tarif2->setBorneMin(501);
        $tarif2->setBorneMax(1500);
        $tarif2->setCout(100);
        $manager->persist($tarif2);

        $tarif3 = new Tarifs();
        $tarif3->setBorneMin(1501);
        $tarif3->setBorneMax(3000);
        $tarif3->setCout(200);
        $manager->persist($tarif3);

        $tarif4 = new Tarifs();
        $tarif4->setBorneMin(3001);
        $tarif4->setBorneMax(5000);
        $tarif4->setCout(400);
        $manager->persist($tarif4);

        $tarif5 = new Tarifs();
        $tarif5->setBorneMin(5001);
        $tarif5->setBorneMax(10000);
        $tarif5->setCout(700);
        $manager->persist($tarif5);

        $tarif6 = new Tarifs();
        $tarif6->setBorneMin(10001);
        $tarif6->setBorneMax(15000);
        $tarif6->setCout(1100);
        $manager->persist($tarif6);

        $tarif7 = new Tarifs();
        $tarif7->setBorneMin(15001);
        $tarif7->setBorneMax(20000);
        $tarif7->setCout(1300);
        $manager->persist($tarif7);

        $tarif8 = new Tarifs();
        $tarif8->setBorneMin(20001);
        $tarif8->setBorneMax(25000);
        $tarif8->setCout(1500);
        $manager->persist($tarif8);

        $tarif9 = new Tarifs();
        $tarif9->setBorneMin(25001);
        $tarif9->setBorneMax(30000);
        $tarif9->setCout(1700);
        $manager->persist($tarif9);

        $tarif10 = new Tarifs();
        $tarif10->setBorneMin(30001);
        $tarif10->setBorneMax(50000);
        $tarif10->setCout(1800);
        $manager->persist($tarif10);

        $tarif11 = new Tarifs();
        $tarif11->setBorneMin(50001);
        $tarif11->setBorneMax(60000);
        $tarif11->setCout(2300);
        $manager->persist($tarif11);

        $tarif12 = new Tarifs();
        $tarif12->setBorneMin(60001);
        $tarif12->setBorneMax(75000);
        $tarif12->setCout(2700);
        $manager->persist($tarif12);

        $tarif13 = new Tarifs();
        $tarif13->setBorneMin(75001);
        $tarif13->setBorneMax(100000);
        $tarif13->setCout(3200);
        $manager->persist($tarif13);

        $tarif14 = new Tarifs();
        $tarif14->setBorneMin(100001);
        $tarif14->setBorneMax(125000);
        $tarif14->setCout(3600);
        $manager->persist($tarif14);

        $tarif15 = new Tarifs();
        $tarif15->setBorneMin(125001);
        $tarif15->setBorneMax(150000);
        $tarif15->setCout(4000);
        $manager->persist($tarif15);

        $tarif16 = new Tarifs();
        $tarif16->setBorneMin(150001);
        $tarif16->setBorneMax(200000);
        $tarif16->setCout(4800);
        $manager->persist($tarif16);

        $tarif17 = new Tarifs();
        $tarif17->setBorneMin(200001);
        $tarif17->setBorneMax(250000);
        $tarif17->setCout(6350);
        $manager->persist($tarif17);

        $tarif18 = new Tarifs();
        $tarif18->setBorneMin(250001);
        $tarif18->setBorneMax(300000);
        $tarif18->setCout(8050);
        $manager->persist($tarif18);

        $tarif19 = new Tarifs();
        $tarif19->setBorneMin(300001);
        $tarif19->setBorneMax(350000);
        $tarif19->setCout(8450);
        $manager->persist($tarif19);

        $tarif20 = new Tarifs();
        $tarif20->setBorneMin(350001);
        $tarif20->setBorneMax(400000);
        $tarif20->setCout(9750);
        $manager->persist($tarif20);

        $tarif21 = new Tarifs();
        $tarif21->setBorneMin(400001);
        $tarif21->setBorneMax(600000);
        $tarif21->setCout(11850);
        $manager->persist($tarif21);

        $tarif22 = new Tarifs();
        $tarif22->setBorneMin(600001);
        $tarif22->setBorneMax(750000);
        $tarif22->setCout(13550);
        $manager->persist($tarif22);

        $tarif23 = new Tarifs();
        $tarif23->setBorneMin(750001);
        $tarif23->setBorneMax(1000000);
        $tarif23->setCout(21650);
        $manager->persist($tarif23);

        $tarif24 = new Tarifs();
        $tarif24->setBorneMin(1000001);
        $tarif24->setBorneMax(1250000);
        $tarif24->setCout(24200);
        $manager->persist($tarif24);

        $tarif25 = new Tarifs();
        $tarif25->setBorneMin(1250001);
        $tarif25->setBorneMax(1500000);
        $tarif25->setCout(31850);
        $manager->persist($tarif25);

        $tarif26 = new Tarifs();
        $tarif26->setBorneMin(1500001);
        $tarif26->setBorneMax(2000000);
        $tarif26->setCout(35650);
        $manager->persist($tarif26);
        
        $manager->flush();
    }
}
