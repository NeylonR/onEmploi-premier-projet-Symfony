<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Company;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($i =0; $i < 2; $i++){
            $faker = Factory::create('fr_FR');
            $company = new Company();
            $company->setName($faker->firstName)
                    ->setCity($faker->city)
                    ->setRoles(['USER'])
                    ->setPassword('123')
                    ->setEmail('mail@mail.mail');
            $manager->persist($company);
        }

        $manager->flush();
    }
}
