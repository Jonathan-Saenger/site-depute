<?php

namespace App\Command;

use App\Entity\Rencontre;
use App\Repository\RencontreRepository;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-rencontre-slugs',
    description: 'Génère les slugs manquants pour les rencontres existantes',
)]
class GenerateRencontreSlugCommand extends Command
{
    public function __construct(
        private RencontreRepository $rencontreRepository,
        private SlugGenerator $slugGenerator,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Génération des slugs pour les rencontres');

        // Récupération des rencontres sans slug
        $rencontres = $this->rencontreRepository->createQueryBuilder('r')
            ->where('r.slug IS NULL OR r.slug = :empty')
            ->setParameter('empty', '')
            ->getQuery()
            ->getResult();

        if (empty($rencontres)) {
            $io->success('Toutes les rencontres ont déjà un slug.');
            return Command::SUCCESS;
        }

        $io->progressStart(count($rencontres));

        $updated = 0;
        foreach ($rencontres as $rencontre) {
            if (empty($rencontre->getTitre())) {
                $io->warning(sprintf('Rencontre #%d n\'a pas de titre, ignorée.', $rencontre->getId()));
                continue;
            }

            // Fonction de vérification d'unicité
            $existsCallback = function (string $slug) use ($rencontre): bool {
                $existingRencontre = $this->rencontreRepository->findOneBy(['slug' => $slug]);

                if (!$existingRencontre) {
                    return false;
                }

                return $existingRencontre->getId() !== $rencontre->getId();
            };

            $slug = $this->slugGenerator->generateUnique($rencontre->getTitre(), $existsCallback);
            $rencontre->setSlug($slug);
            $updated++;

            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $io->progressFinish();

        $io->success(sprintf('%d slugs ont été générés avec succès.', $updated));

        return Command::SUCCESS;
    }
}
