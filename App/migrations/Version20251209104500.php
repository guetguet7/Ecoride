<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajoute le type de rôle choisi par l'utilisateur (passenger/driver/both).
 */
final class Version20251209104500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la colonne role_type (VARCHAR, default passenger) à la table user.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('user');

        if (!$table->hasColumn('role_type')) {
            $this->addSql("ALTER TABLE user ADD role_type VARCHAR(20) NOT NULL DEFAULT 'passenger'");
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('user');

        if ($table->hasColumn('role_type')) {
            $this->addSql('ALTER TABLE user DROP role_type');
        }
    }
}
