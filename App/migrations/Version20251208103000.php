<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Crée la table participation (user_id, ride_id, amount, status, created_at).
 */
final class Version20251208103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Table participation pour enregistrer les trajets rejoints par les utilisateurs.';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $exists = $schemaManager->tablesExist(['participation']);

        if (!$exists) {
            $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ride_id INT NOT NULL, amount INT NOT NULL, status VARCHAR(20) NOT NULL DEFAULT \'confirmed\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_PARTICIPATION_USER (user_id), INDEX IDX_PARTICIPATION_RIDE (ride_id), UNIQUE INDEX UNIQ_USER_RIDE (user_id, ride_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_PARTICIPATION_USER FOREIGN KEY (user_id) REFERENCES user (id)');
            $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_PARTICIPATION_RIDE FOREIGN KEY (ride_id) REFERENCES rides (id)');
        }
    }

    public function down(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $exists = $schemaManager->tablesExist(['participation']);

        if ($exists) {
            $this->addSql('DROP TABLE participation');
        }
    }
}
