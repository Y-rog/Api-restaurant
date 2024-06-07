<?php

namespace App\DataFixtures;

use App\Entity\Food;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FoodFixtures extends Fixture
{
    public const FOOD_NB_TUPLES = 20;
    public const FOOD_REFERENCE = "food";

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::FOOD_NB_TUPLES; $i++) {
            $food = (new Food())
                ->setTitle($faker->sentence(20))
                ->setDescription($faker->text(200))
                ->setPrice($faker->randomFloat(2, 10, 100))
                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($food);
            $this->addReference(self::FOOD_REFERENCE . $i, $food);
        }

        $manager->flush();
    }
}
