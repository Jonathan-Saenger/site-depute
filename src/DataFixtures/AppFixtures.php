<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Article;
use App\Enum\CategoryEnum;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i=0; $i < 10; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence(3));
            $article->setSlug($faker->slug());
            $article->setContent($faker->paragraph(5));
            $article->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 year')->format('Y-m-d')));
            $article->setIsPublished(true);
            $article->setCategory($faker->randomElement([CategoryEnum::ASSEMBLEE, CategoryEnum::CIRCONSCRIPTION]));
            $article->setImageUrl($faker->imageUrl(640, 480, 'politics'));
            $article->setImageUrl("https://picsum.photos/640/480?random=" . $i);

            $manager->persist($article);
        }
        $manager->flush();
    }
}
