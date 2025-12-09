<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajoute la note moyenne (rating) sur l'utilisateur.
 */
final class Version20251209101500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la colonne rating (FLOAT DEFAULT 0) à la table user.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('user');

        if (!$table->hasColumn('rating')) {
            $this->addSql('ALTER TABLE user ADD rating DOUBLE PRECISION NOT NULL DEFAULT 0');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('user');

        if ($table->hasColumn('rating')) {
            $this->addSql('ALTER TABLE user DROP rating');
        }
    }
}
