<?php

namespace App\DataFixtures;

use App\Entity\Phone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PhoneFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $colours = ['white', 'noir', 'chrome', 'gold'];
        $brand = ['samsung', 'iphone'];

        // create 15 phones
        for ($i = 0; $i < 15; $i++) {
            $phone = new Phone();
            $phone->setbrand($brand[array_rand($brand)])
                  ->setName($phone->getBrand() . ' ' . mt_rand(5, 10))
                  ->setPrice(mt_rand(100, 900))
                  ->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.')               
                  ->setColour($colours[array_rand($colours)]);
                
                  $manager->persist($phone);
            }

        $manager->flush();
    }
}
