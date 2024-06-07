<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public const CATEGORY_NB_TUPLES = 3;
    public const CATEGORY_REFERENCE = "category";

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::CATEGORY_NB_TUPLES; $i++) {
            $category = (new Category())
                ->setTitle($faker->word())
                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($category);
            $this->addReference(self::CATEGORY_REFERENCE . $i, $category);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['category'];
    }

    public function getDependencies(): array
    {
        return [
            RestaurantFixtures::class,
        ];
    }
}
