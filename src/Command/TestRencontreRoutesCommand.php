<?php

namespace App\Command;

use App\Repository\RencontreRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'app:test:rencontre-routes',
    description: 'Teste toutes les routes avec slugs pour les rencontres',
)]
class TestRencontreRoutesCommand extends Command
{
    public function __construct(
        private RencontreRepository $rencontreRepository,
        private RouterInterface $router
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Test des routes Rencontre avec slugs');

        // Récupération d'une rencontre de test
        $rencontre = $this->rencontreRepository->findOneBy([]);

        if (!$rencontre) {
            $io->error('Aucune rencontre trouvée dans la base de données.');
            return Command::FAILURE;
        }

        if (!$rencontre->getSlug()) {
            $io->error('La rencontre n\'a pas de slug. Exécutez d\'abord app:generate-rencontre-slugs');
            return Command::FAILURE;
        }

        $slug = $rencontre->getSlug();
        $io->info(sprintf('Test avec la rencontre: "%s" (slug: %s)', $rencontre->getTitre(), $slug));

        // Test des routes
        $routes = [
            'app_admin_rencontre_index' => [],
            'app_admin_rencontre_new' => [],
            'app_admin_rencontre_show' => ['slug' => $slug],
            'app_admin_rencontre_edit' => ['slug' => $slug],
            'app_admin_rencontre_delete' => ['slug' => $slug],
            'app_admin_rencontre_toggle_visible' => ['slug' => $slug],
        ];

        $io->progressStart(count($routes));

        $success = 0;
        $errors = [];

        foreach ($routes as $routeName => $parameters) {
            try {
                $url = $this->router->generate($routeName, $parameters);
                $io->progressAdvance();
                $success++;

                // Pour les routes avec paramètres, vérifier que le slug est bien dans l'URL
                if (!empty($parameters)) {
                    if (strpos($url, $slug) === false) {
                        $errors[] = sprintf('Route %s: le slug n\'est pas dans l\'URL (%s)', $routeName, $url);
                    }
                }
            } catch (\Exception $e) {
                $errors[] = sprintf('Route %s: %s', $routeName, $e->getMessage());
                $io->progressAdvance();
            }
        }

        $io->progressFinish();

        if ($success === count($routes) && empty($errors)) {
            $io->success(sprintf('Toutes les %d routes fonctionnent correctement !', $success));

            // Affichage des URLs générées
            $io->section('URLs générées:');
            foreach ($routes as $routeName => $parameters) {
                $url = $this->router->generate($routeName, $parameters);
                $io->writeln(sprintf('• %s: %s', $routeName, $url));
            }

            return Command::SUCCESS;
        } else {
            $io->error(sprintf('%d/%d routes échouées', count($errors), count($routes)));
            foreach ($errors as $error) {
                $io->writeln('❌ ' . $error);
            }
            return Command::FAILURE;
        }
    }
}
