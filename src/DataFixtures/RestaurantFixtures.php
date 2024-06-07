<?php

namespace App\DataFixtures;

use App\Entity\Restaurant;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class RestaurantFixtures extends Fixture implements DependentFixtureInterface
{
    public const RESTAURANT_REFERENCE = "restaurant";
    public const RESTAURANT_NB_TUPLES = 20;

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::RESTAURANT_NB_TUPLES; $i++) {
            $restaurant = (new Restaurant())
                ->setName($faker->company())
                ->setDescription($faker->text(200))
                ->setAmOpeningTime([])
                ->setPmOpeningTime([])
                ->setMaxGuest(random_int(1, 50))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setOwner($this->getReference(UserFixtures::USER_REFERENCE . $i));

            $manager->persist($restaurant);
            $this->addReference(self::RESTAURANT_REFERENCE . $i, $restaurant);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['restaurant'];
    }
}
