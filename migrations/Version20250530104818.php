<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250530104818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Suppression de la colonne slug de la table rencontre pour revenir aux IDs';
    }    public function up(Schema $schema): void
    {
        // Suppression de la colonne slug
        $this->addSql(<<<'SQL'
            ALTER TABLE rencontre DROP COLUMN slug
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Ajout de la colonne slug
        $this->addSql(<<<'SQL'
            ALTER TABLE rencontre ADD slug VARCHAR(255) NOT NULL
        SQL);

        // Recréation de l'index unique
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_460C35ED989D9B62 ON rencontre (slug)
        SQL);
    }
}
