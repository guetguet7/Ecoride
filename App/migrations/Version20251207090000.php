<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajoute la relation rides -> user (ManyToOne) et index.
 */
final class Version20251207090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la colonne user_id sur rides et un index pour relier les trajets aux utilisateurs.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('rides');

        if (!$table->hasColumn('user_id')) {
            $this->addSql('ALTER TABLE rides ADD user_id INT DEFAULT NULL');
        }

        $hasFk = false;
        foreach ($table->getForeignKeys() as $fk) {
            if ($fk->getName() === 'FK_rides_user') {
                $hasFk = true;
                break;
            }
        }
        if (!$hasFk) {
            $this->addSql('ALTER TABLE rides ADD CONSTRAINT FK_rides_user FOREIGN KEY (user_id) REFERENCES user (id)');
        }

        if (!$table->hasIndex('IDX_rides_user')) {
            $this->addSql('CREATE INDEX IDX_rides_user ON rides (user_id)');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('rides');

        if ($table->hasIndex('IDX_rides_user')) {
            $this->addSql('DROP INDEX IDX_rides_user ON rides');
        }

        $hasFk = false;
        foreach ($table->getForeignKeys() as $fk) {
            if ($fk->getName() === 'FK_rides_user') {
                $hasFk = true;
                break;
            }
        }
        if ($hasFk) {
            $this->addSql('ALTER TABLE rides DROP FOREIGN KEY FK_rides_user');
        }

        if ($table->hasColumn('user_id')) {
            $this->addSql('ALTER TABLE rides DROP COLUMN user_id');
        }
    }
}
