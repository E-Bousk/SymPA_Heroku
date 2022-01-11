<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Faker;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        for ($nbUsers = 1; $nbUsers <= 30; $nbUsers++) {
            $user = new Users();
            if ($nbUsers === 1) {
                $user->setEmail('admin@gmail.com');
                $user->setRoles(['ROLE_ADMIN']); 
                $user->setPassword($this->encoder->encodePassword($user, 'root'));
            } else {
                $user->setEmail($faker->freeEmail);
                $user->setPassword($this->encoder->encodePassword($user, 'password'));
            }
            $user->setName($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setIsVerified($faker->boolean(75));
            $manager->persist($user);

            // Enregistre l'utilisateur dans une référence
            $this->addReference('user_' . $nbUsers, $user);

        }
        $manager->flush();
    }
}
