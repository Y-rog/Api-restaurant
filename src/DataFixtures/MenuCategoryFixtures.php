<?php

namespace App\DataFixtures;

use App\Entity\MenuCategory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MenuCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public const MENU_CATEGORY_NB_TUPLES = 20;
    public const MENU_CATEGORY_REFERENCE = "menu_category";

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= self::MENU_CATEGORY_NB_TUPLES; $i++) {
            $menuCategory = (new MenuCategory())
                ->setMenu($this->getReference(MenuFixtures::MENU_REFERENCE . random_int(1, 20)))
                ->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE . random_int(1, 3)));

            $manager->persist($menuCategory);
            $this->addReference(self::MENU_CATEGORY_REFERENCE . $i, $menuCategory);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MenuFixtures::class,
            CategoryFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['menu_category'];
    }
}
