<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adjust user_profile schema to match the entity definition.
 */
final class Version20251203200744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Met à jour la table user_profile (types, relations et contraintes).';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('user_profile');

        $this->addSql('ALTER TABLE user_profile CHANGE nom nom VARCHAR(100) DEFAULT NULL, CHANGE prenom prenom VARCHAR(100) DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE date_naissance date_naissance DATETIME DEFAULT NULL COMMENT "(DC2Type:datetime_immutable)", CHANGE photo photo VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE user_profile DROP COLUMN IF EXISTS user');

        if (!$table->hasColumn('user_id')) {
            $this->addSql('ALTER TABLE user_profile ADD user_id INT NOT NULL');
        }

        $hasForeignKey = false;
        foreach ($table->getForeignKeys() as $foreignKey) {
            if ($foreignKey->getName() === 'FK_D95AB405A76ED395') {
                $hasForeignKey = true;
                break;
            }
        }

        if (!$hasForeignKey) {
            $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        }

        if (!$table->hasIndex('UNIQ_D95AB405A76ED395')) {
            $this->addSql('CREATE UNIQUE INDEX UNIQ_D95AB405A76ED395 ON user_profile (user_id)');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $table = $schemaManager->introspectTable('user_profile');

        $hasForeignKey = false;
        foreach ($table->getForeignKeys() as $foreignKey) {
            if ($foreignKey->getName() === 'FK_D95AB405A76ED395') {
                $hasForeignKey = true;
                break;
            }
        }
        if ($hasForeignKey) {
            $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405A76ED395');
        }

        if ($table->hasIndex('UNIQ_D95AB405A76ED395')) {
            $this->addSql('DROP INDEX UNIQ_D95AB405A76ED395 ON user_profile');
        }

        if ($table->hasColumn('user_id')) {
            $this->addSql('ALTER TABLE user_profile DROP COLUMN IF EXISTS user_id');
        }

        if (!$table->hasColumn('user')) {
            $this->addSql('ALTER TABLE user_profile ADD user VARCHAR(255) DEFAULT NULL');
        }

        $this->addSql('ALTER TABLE user_profile CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL, CHANGE telephone telephone VARCHAR(255) DEFAULT NULL, CHANGE date_naissance date_naissance VARCHAR(255) DEFAULT NULL, CHANGE photo photo LONGBLOB DEFAULT NULL');
    }
}
