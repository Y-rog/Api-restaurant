<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PictureFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws \Exception
     */

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {

        for ($i = 1; $i <= 20; ++$i) {
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
}
