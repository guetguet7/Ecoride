<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Rend la relation voiture -> user multiple (suppression de l'index unique).
 */
final class Version20251206090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Supprime l’index unique sur voiture.user_id pour permettre plusieurs voitures par utilisateur.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('voiture');

        if ($table->hasIndex('UNIQ_voiture_user')) {
            $this->addSql('DROP INDEX UNIQ_voiture_user ON voiture');
        }

        if (!$table->hasIndex('IDX_voiture_user')) {
            $this->addSql('CREATE INDEX IDX_voiture_user ON voiture (user_id)');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('voiture');

        if ($table->hasIndex('IDX_voiture_user')) {
            $this->addSql('DROP INDEX IDX_voiture_user ON voiture');
        }

        if (!$table->hasIndex('UNIQ_voiture_user')) {
            $this->addSql('CREATE UNIQUE INDEX UNIQ_voiture_user ON voiture (user_id)');
        }
    }
}
