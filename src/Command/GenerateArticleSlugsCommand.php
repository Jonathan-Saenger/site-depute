<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Article;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:article:generate-slugs',
    description: 'Génère les slugs manquants pour les articles existants',
)]
final class GenerateArticleSlugsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SlugGenerator $slugGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force la régénération même si un slug existe déjà')
            ->setHelp('Cette commande génère les slugs manquants pour tous les articles existants.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $force = $input->getOption('force');

        $repository = $this->entityManager->getRepository(Article::class);

        // Récupérer tous les articles
        $articles = $repository->findAll();

        if (empty($articles)) {
            $io->info('Aucun article trouvé.');
            return Command::SUCCESS;
        }

        $updated = 0;
        $total = count($articles);

        $io->progressStart($total);

        foreach ($articles as $article) {
            // Générer le slug si manquant ou si force est activé
            if (empty($article->getSlug()) || $force) {
                $title = $article->getTitle();
                if (!empty($title)) {
                    // Fonction pour vérifier l'unicité
                    $existsCallback = function (string $slug) use ($article): bool {
                        $existing = $this->entityManager
                            ->getRepository(Article::class)
                            ->findOneBy(['slug' => $slug]);

                        return $existing && $existing->getId() !== $article->getId();
                    };

                    $slug = $this->slugGenerator->generateUnique($title, $existsCallback);
                    $article->setSlug($slug);
                    $updated++;
                }
            }

            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $io->progressFinish();

        $io->success(sprintf(
            '%d slug(s) générés sur %d articles.',
            $updated,
            $total
        ));

        return Command::SUCCESS;
    }
}
