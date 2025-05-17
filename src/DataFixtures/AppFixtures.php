<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Rencontre;
use App\Enum\CategoryEnum;
use App\Enum\CommuneEnum;
use App\Enum\RencontreEnum;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Fixtures pour les articles
        for ($i = 0; $i < 10; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence(3));
            $article->setSlug($faker->slug());
            $article->setContent($faker->paragraph(5));
            $article->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 year')->format('Y-m-d')));
            $article->setIsPublished(true);
            $article->setCategory($faker->randomElement([CategoryEnum::ASSEMBLEE, CategoryEnum::CIRCONSCRIPTION]));
            $article->setImageUrl("https://picsum.photos/640/480?random=" . $i);

            $manager->persist($article);
        }

        // Fixtures pour les rencontres
        for ($i = 0; $i < 10; $i++) {
            $rencontre = new Rencontre();
            $rencontre->setTitre($faker->sentence(3));
            $rencontre->setDescription($faker->paragraph(2));
            $rencontre->setDate($faker->dateTimeBetween('now', '+1 year'));
            $rencontre->setLieu($faker->address);
            $rencontre->setCommune($faker->randomElement(CommuneEnum::cases()));
            $rencontre->setType($faker->randomElement(RencontreEnum::cases()));
            $rencontre->setVisible(true);

            $manager->persist($rencontre);
        }

        $manager->flush();
    }
}
