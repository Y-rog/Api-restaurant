<?php

namespace App\DataFixtures;

use App\Entity\Menu;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MenuFixtures extends Fixture implements DependentFixtureInterface
{
    public const MENU_NB_TUPLES = 20;
    public const MENU_REFERENCE = "menu";

    /**
     * @throws \Exception
     */

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::MENU_NB_TUPLES; $i++) {
            $menu = (new Menu())
                ->setTitle($faker->sentence(3))
                ->setDescription($faker->text(200))
                ->setPrice($faker->randomFloat(2, 10, 100))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setRestaurant($this->getReference(RestaurantFixtures::RESTAURANT_REFERENCE . random_int(1, 20)));

            $manager->persist($menu);
            $this->addReference(self::MENU_REFERENCE . $i, $menu);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['menu'];
    }

    public function getDependencies(): array
    {
        return [
            RestaurantFixtures::class,
        ];
    }
}
