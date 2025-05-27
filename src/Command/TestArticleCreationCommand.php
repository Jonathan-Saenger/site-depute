<?php

namespace App\Command;

use App\Entity\Article;
use App\Enum\CategoryEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-article-creation',
    description: 'Test de création d\'un article pour valider les timestamps automatiques',
)]
class TestArticleCreationCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Test de création d\'un article avec timestamps automatiques');

        try {
            // Création d'un nouvel article
            $article = new Article();

            $io->text('Article instancié avec succès');
            $io->text('CreatedAt automatique: ' . $article->getCreatedAt()->format('Y-m-d H:i:s'));
            $io->text('IsPublished par défaut: ' . ($article->isPublished() ? 'true' : 'false'));

            // Configuration de l'article
            $article->setTitle('Test Article - ' . date('Y-m-d H:i:s'));
            $article->setContent('Ceci est un test de création d\'article avec timestamps automatiques.');
            $article->setSlug('test-article-' . time());
            $article->setImageUrl('https://picsum.photos/640/480?random=' . rand(1, 1000));
            $article->setCategory(CategoryEnum::ASSEMBLEE);
            $article->setIsPublished(true);

            // Persistance en base de données
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $io->success('Article créé avec succès en base de données !');
            $io->table(['Propriété', 'Valeur'], [
                ['ID', $article->getId()],
                ['Titre', $article->getTitle()],
                ['Slug', $article->getSlug()],
                ['Catégorie', $article->getCategory()->value],
                ['CreatedAt', $article->getCreatedAt()->format('Y-m-d H:i:s')],
                ['UpdateAt', $article->getUpdateAt() ? $article->getUpdateAt()->format('Y-m-d H:i:s') : 'null'],
                ['IsPublished', $article->isPublished() ? 'true' : 'false'],
            ]);

            // Test de mise à jour
            $io->section('Test de mise à jour');

            $article->setTitle($article->getTitle() . ' [MODIFIÉ]');
            $this->entityManager->flush();

            $io->text('Article mis à jour');
            $io->text('UpdateAt après modification: ' . ($article->getUpdateAt() ? $article->getUpdateAt()->format('Y-m-d H:i:s') : 'null'));

            $io->success('Test terminé avec succès ! Le problème de contrainte d\'intégrité est résolu.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Erreur lors du test: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
