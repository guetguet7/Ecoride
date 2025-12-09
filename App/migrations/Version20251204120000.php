<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajoute un solde de crédits aux utilisateurs.
 */
final class Version20251204120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la colonne credits (INT DEFAULT 0) à la table user.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('user');

        if (!$table->hasColumn('credits')) {
            $this->addSql('ALTER TABLE user ADD credits INT NOT NULL DEFAULT 0');
            $this->addSql('UPDATE user SET credits = 0 WHERE credits IS NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('user');

        if ($table->hasColumn('credits')) {
            $this->addSql('ALTER TABLE user DROP COLUMN IF EXISTS credits');
        }
    }
}
