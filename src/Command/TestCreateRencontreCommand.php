<?php

namespace App\Command;

use App\Entity\Rencontre;
use App\Enum\CommuneEnum;
use App\Enum\RencontreEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test:create-rencontre',
    description: 'Teste la création d\'une rencontre pour valider la génération automatique de slugs',
)]
class TestCreateRencontreCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Test de création d\'une rencontre avec génération automatique de slug');        // Création d'une nouvelle rencontre
        $rencontre = new Rencontre();
        $rencontre->setTitre('Rencontre de test avec les citoyens de Ferrette');
        $rencontre->setDescription('Une rencontre importante pour échanger avec les habitants du quartier');
        $rencontre->setDate(new \DateTime('+1 week'));
        $rencontre->setLieu('Mairie de quartier');
        $rencontre->setCommune(CommuneEnum::FERRETTE);
        $rencontre->setType(RencontreEnum::PUBLIQUE);

        $io->info('Avant persist - Slug: ' . ($rencontre->getSlug() ?? 'NULL'));

        // Persist et flush pour déclencher l'EventListener
        $this->entityManager->persist($rencontre);
        $this->entityManager->flush();

        $io->info('Après flush - Slug: ' . ($rencontre->getSlug() ?? 'NULL'));

        if ($rencontre->getSlug()) {
            $io->success(sprintf(
                'Rencontre créée avec succès !' . PHP_EOL .
                'ID: %d' . PHP_EOL .
                'Titre: %s' . PHP_EOL .
                'Slug: %s' . PHP_EOL .
                'URL d\'affichage: /dashboard/rencontre/%s',
                $rencontre->getId(),
                $rencontre->getTitre(),
                $rencontre->getSlug(),
                $rencontre->getSlug()
            ));
            return Command::SUCCESS;
        } else {
            $io->error('Erreur: aucun slug n\'a été généré pour la rencontre');
            return Command::FAILURE;
        }
    }
}
