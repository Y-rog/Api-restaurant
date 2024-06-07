<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PictureFixtures extends Fixture implements DependentFixtureInterface
{

    public const PICTURE_NB_TUPLES = 20;

    /**
     * @throws \Exception
     */

    public function load(ObjectManager $manager): void
    {

        for ($i = 1; $i <= self::PICTURE_NB_TUPLES; ++$i) {
            $picture = (new Picture())
                ->setTitle('Picture' . $i)
                ->setSlug('slug-article-title-' . $i)
                ->setRestaurant($this->getReference(RestaurantFixtures::RESTAURANT_REFERENCE . random_int(1, 20)))
                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($picture);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RestaurantFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['picture'];
    }
}
