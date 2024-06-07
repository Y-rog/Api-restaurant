<?php

namespace App\DataFixtures;

use App\Entity\FoodCategory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FoodCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public const FOOD_CATEGORY_NB_TUPLES = 20;
    public const FOOD_CATEGORY_REFERENCE = "food_category";

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= self::FOOD_CATEGORY_NB_TUPLES; $i++) {
            $foodCategory = (new FoodCategory())
                ->setFood($this->getReference(FoodFixtures::FOOD_REFERENCE . random_int(1, 20)))
                ->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE . random_int(1, 3)));

            $manager->persist($foodCategory);
            $this->addReference(self::FOOD_CATEGORY_REFERENCE . $i, $foodCategory);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FoodFixtures::class,
            CategoryFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['food_category'];
    }
}
