<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\EndUser;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class ClientFixtures extends Fixture
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $client = ['Orange', 'SFR', 'Bouygues', 'Free'];

        // Generate faker to create fake data
        $faker = Factory::create();

        foreach ($client as $provider) {

            $client = new Client();
            $client->setUsername($provider)
                   ->setEmail($provider . "@" . $provider . ".com" )
                   ->setRoles(['ROLE_USER'])
                   ->setPassword($faker->password());
            
            // Create a set of endUsers attached to this client
            for ($j = 0; $j < 10; $j++) {
                $endUser = new endUser();
                $endUser->setName($faker->name())
                        ->setEmail($faker->email())
                        ->setClient($client);
                // Save the info for this endUser
                $manager->persist($endUser);
            }
            
            $manager->persist($client);
        }

        // Create a test User for the demo
        $client = new Client();
        $client->setEmail('testUser@mail.com')
               ->setUsername('testuser')
               ->setRoles(['ROLE_USER'])
               ->setPassword($this->passwordHasher->hashPassword($client,'testuser'));
        
        $manager->persist($client);

        // Create a set of endUsers attached to this client
        for ($i = 0; $i < 10; $i++) {
            $endUser = new endUser();
            $endUser->setName($faker->name())
                    ->setEmail($faker->email())
                    ->setClient($client);
            // Save the info for this endUser
            $manager->persist($endUser);
        }

        $manager->flush();
    }
}
