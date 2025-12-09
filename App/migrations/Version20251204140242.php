<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204140242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rides CHANGE date_heure_depart date_heure_depart DATETIME NOT NULL, CHANGE date_heure_arrivee date_heure_arrivee DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rides CHANGE date_heure_depart date_heure_depart DATE NOT NULL, CHANGE date_heure_arrivee date_heure_arrivee DATE NOT NULL');
    }
}
