<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BookingFixtures extends Fixture implements DependentFixtureInterface
{
    public const BOOKING_NB_TUPLES = 20;
    public const BOOKING_REFERENCE = "booking";

    /**
     * @throws \Exception
     */

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::BOOKING_NB_TUPLES; $i++) {
            $booking = (new Booking())
                ->setGuestNumber(random_int(1, 10))
                ->setOrderDate(new \DateTime())
                ->setOrderHour(new \DateTime())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setAllergy($faker->text(50))
                ->setClient($this->getReference(UserFixtures::USER_REFERENCE . random_int(1, 20)))
                ->setRestaurant($this->getReference(RestaurantFixtures::RESTAURANT_REFERENCE . random_int(1, 20)));


            $manager->persist($booking);
            $this->addReference(self::BOOKING_REFERENCE . $i, $booking);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['booking'];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            RestaurantFixtures::class,
        ];
    }
}
