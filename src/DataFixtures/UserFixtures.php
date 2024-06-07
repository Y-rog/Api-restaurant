<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_NB_TUPLES = 20;
    public const USER_REFERENCE = "user";


    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @throws \Exception
     */

    public function load(ObjectManager $manager): void
    {

        for ($i = 1; $i <= self::USER_NB_TUPLES; $i++) {
            $faker = \Faker\Factory::create('fr_FR');

            $user = (new User())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setGuestNumber(random_int(1, 10))
                ->setEmail($faker->email())
                ->setCreatedAt(new \DateTimeImmutable());

            $user->setPassword($this->passwordHasher->hashPassword($user, 'password' . $i));

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE . $i, $user);
        }

        $manager->flush();
    }

    public function getGroups(): array
    {
        return ['user'];
    }
}
