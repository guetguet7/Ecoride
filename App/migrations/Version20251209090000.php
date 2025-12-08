<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Passe les dates de départ/arrivée des trajets en DATETIME (avec heure).
 */
final class Version20251209090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Alter table rides: dateHeureDepart/dateHeureArrivee en DATETIME pour conserver l\'heure.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rides CHANGE date_heure_depart date_heure_depart DATETIME NOT NULL, CHANGE date_heure_arrivee date_heure_arrivee DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE rides CHANGE date_heure_depart date_heure_depart DATE NOT NULL, CHANGE date_heure_arrivee date_heure_arrivee DATE NOT NULL');
    }
}
