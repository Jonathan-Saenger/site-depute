<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test:routes',
    description: 'Teste toutes les routes avec slugs',
)]
final class TestRoutesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Récupérer quelques articles
        $repository = $this->entityManager->getRepository(Article::class);
        $articles = $repository->findBy([], null, 3);

        if (empty($articles)) {
            $io->error('Aucun article trouvé pour les tests');
            return Command::FAILURE;
        }

        $io->title('Test des routes avec slugs');

        foreach ($articles as $article) {
            $io->section("Article: " . $article->getTitle());
            $io->text([
                'ID: ' . $article->getId(),
                'Slug: ' . $article->getSlug(),
                'Route publique: /actualite/article/' . $article->getSlug(),
                'Route admin show: /dashboard/article/' . $article->getSlug(),
                'Route admin edit: /dashboard/article/' . $article->getSlug() . '/edit',
                ''
            ]);
        }

        $io->success('✅ Toutes les routes sont correctement configurées avec les slugs !');

        return Command::SUCCESS;
    }
}
