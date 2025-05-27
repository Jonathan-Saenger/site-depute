<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527210329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de l\'index unique sur le champ slug des articles';
    }

    public function up(Schema $schema): void
    {
        // Générer les slugs manquants avant d'ajouter l'index unique
        $this->addSql(<<<'SQL'
            UPDATE article
            SET slug = LOWER(
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                REPLACE(
                                                    REPLACE(title, 'é', 'e'),
                                                'è', 'e'),
                                            'ê', 'e'),
                                        'à', 'a'),
                                    'ç', 'c'),
                                ' ', '-'),
                            '/', '-'),
                        '(', ''),
                    ')', ''),
                ':', '')
            )
            WHERE slug IS NULL OR slug = ''
        SQL);

        // Ajouter l'index unique
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_23A0E66989D9B62 ON article (slug)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_23A0E66989D9B62 ON article
        SQL);
    }
}
