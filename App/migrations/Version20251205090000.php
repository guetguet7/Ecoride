<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Lie une voiture à un utilisateur via un OneToOne.
 */
final class Version20251205090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la relation OneToOne voiture <-> user (colonne user_id unique).';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('voiture');

        if (!$table->hasColumn('user_id')) {
            $this->addSql('ALTER TABLE voiture ADD user_id INT NOT NULL');
        }

        $hasFk = false;
        foreach ($table->getForeignKeys() as $fk) {
            if ($fk->getName() === 'FK_voiture_user') {
                $hasFk = true;
                break;
            }
        }
        if (!$hasFk) {
            $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_voiture_user FOREIGN KEY (user_id) REFERENCES user (id)');
        }

        if (!$table->hasIndex('UNIQ_voiture_user')) {
            $this->addSql('CREATE UNIQUE INDEX UNIQ_voiture_user ON voiture (user_id)');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('voiture');

        $hasFk = false;
        foreach ($table->getForeignKeys() as $fk) {
            if ($fk->getName() === 'FK_voiture_user') {
                $hasFk = true;
                break;
            }
        }
        if ($hasFk) {
            $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_voiture_user');
        }

        if ($table->hasIndex('UNIQ_voiture_user')) {
            $this->addSql('DROP INDEX UNIQ_voiture_user ON voiture');
        }

        if ($table->hasColumn('user_id')) {
            $this->addSql('ALTER TABLE voiture DROP COLUMN IF EXISTS user_id');
        }
    }
}
