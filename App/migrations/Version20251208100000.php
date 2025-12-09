<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajoute le prix au trajet (rides.prix).
 */
final class Version20251208100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la colonne prix (INT DEFAULT 0) à la table rides.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('rides');

        if (!$table->hasColumn('prix')) {
            $this->addSql('ALTER TABLE rides ADD prix INT NOT NULL DEFAULT 0');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('rides');

        if ($table->hasColumn('prix')) {
            $this->addSql('ALTER TABLE rides DROP COLUMN prix');
        }
    }
}
