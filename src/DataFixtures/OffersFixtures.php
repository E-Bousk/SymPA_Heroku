<?php

namespace App\DataFixtures;

use App\Entity\Annonces;
use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OffersFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($nbOffers=1; $nbOffers <= 50; $nbOffers++) {
            $user = $this->getReference('user_' . $faker->numberBetween(1, 30));
            $categorie = $this->getReference('categorie_' . $faker->numberBetween(1, 4));

            $offer = new Annonces();
            $offer->setUsers($user);
            $offer->setCategories($categorie);
            $offer->setTitle($faker->realText(25));
            $offer->setContent($faker->realText(400));
            $offer->setActive($faker->boolean(75));

            // Upload et génère les images
            for ($image = 1; $image < 4; $image++) {
                $img = $faker->image('public/uploads/images/offers');
                $imageOffer = new Images();
                $imageOffer->setName(basename($img));
                $offer->addImage($imageOffer);
            }
            $manager->persist($offer);
        }
        $manager->flush();
    }

    // Implémente cette méthode pour indiquer de charger d'abord 
    // les fixtures de la table « Users » et « Categories »
    public function getDependencies()
    {
        return [
            CategoriesFixtures::class,
            UsersFixtures::class
        ];
    }
}
