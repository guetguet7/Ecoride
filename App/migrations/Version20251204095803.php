<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204095803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        $userTable = $schemaManager->introspectTable('user');
        if (!$userTable->hasColumn('credits')) {
            $this->addSql('ALTER TABLE user ADD credits INT DEFAULT 0 NOT NULL');
        }

        $profileTable = $schemaManager->introspectTable('user_profile');

        foreach ($profileTable->getForeignKeys() as $fk) {
            if ($fk->getName() === 'FK_user_profile_user') {
                $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY `FK_user_profile_user`');
                break;
            }
        }

        if ($profileTable->hasColumn('user')) {
            $this->addSql('ALTER TABLE user_profile DROP user');
        }

        $this->addSql('ALTER TABLE user_profile CHANGE nom nom VARCHAR(100) DEFAULT NULL, CHANGE prenom prenom VARCHAR(100) DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE date_naissance date_naissance DATETIME DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL');

        $hasFkNew = false;
        foreach ($profileTable->getForeignKeys() as $fk) {
            if ($fk->getName() === 'FK_D95AB405A76ED395') {
                $hasFkNew = true;
                break;
            }
        }
        if (!$hasFkNew) {
            $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        }

        $hasNewIndex = $profileTable->hasIndex('UNIQ_D95AB405A76ED395');
        $hasOldIndex = $profileTable->hasIndex('uniq_user_profile_user');

        if ($hasOldIndex && !$hasNewIndex) {
            $this->addSql('ALTER TABLE user_profile RENAME INDEX uniq_user_profile_user TO UNIQ_D95AB405A76ED395');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        $userTable = $schemaManager->introspectTable('user');
        if ($userTable->hasColumn('credits')) {
            $this->addSql('ALTER TABLE user DROP COLUMN IF EXISTS credits');
        }

        $profileTable = $schemaManager->introspectTable('user_profile');

        foreach ($profileTable->getForeignKeys() as $fk) {
            if ($fk->getName() === 'FK_D95AB405A76ED395') {
                $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405A76ED395');
                break;
            }
        }

        if (!$profileTable->hasColumn('user')) {
            $this->addSql('ALTER TABLE user_profile ADD user VARCHAR(255) DEFAULT NULL');
        }

        $this->addSql('ALTER TABLE user_profile CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL, CHANGE telephone telephone VARCHAR(255) DEFAULT NULL, CHANGE date_naissance date_naissance VARCHAR(255) DEFAULT NULL, CHANGE photo photo LONGBLOB DEFAULT NULL');

        $hasFkOld = false;
        foreach ($profileTable->getForeignKeys() as $fk) {
            if ($fk->getName() === 'FK_user_profile_user') {
                $hasFkOld = true;
                break;
            }
        }
        if (!$hasFkOld) {
            $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT `FK_user_profile_user` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        }

        $hasOldIndex = $profileTable->hasIndex('uniq_user_profile_user');
        $hasNewIndex = $profileTable->hasIndex('uniq_d95ab405a76ed395');

        if ($hasNewIndex && !$hasOldIndex) {
            $this->addSql('ALTER TABLE user_profile RENAME INDEX uniq_d95ab405a76ed395 TO UNIQ_user_profile_user');
        }
    }
}
