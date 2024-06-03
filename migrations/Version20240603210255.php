<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240603210255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trip_passengers (trip_id INT NOT NULL, driver_id INT NOT NULL, INDEX IDX_1645559CA5BC2E0E (trip_id), INDEX IDX_1645559CC3423909 (driver_id), PRIMARY KEY(trip_id, driver_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trip_passengers ADD CONSTRAINT FK_1645559CA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip_passengers ADD CONSTRAINT FK_1645559CC3423909 FOREIGN KEY (driver_id) REFERENCES driver (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trip ADD active TINYINT(1) NOT NULL, CHANGE driver_id main_driver_id INT NOT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BE3F49EAB FOREIGN KEY (main_driver_id) REFERENCES driver (id)');
        $this->addSql('CREATE INDEX IDX_7656F53BE3F49EAB ON trip (main_driver_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip_passengers DROP FOREIGN KEY FK_1645559CA5BC2E0E');
        $this->addSql('ALTER TABLE trip_passengers DROP FOREIGN KEY FK_1645559CC3423909');
        $this->addSql('DROP TABLE trip_passengers');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BE3F49EAB');
        $this->addSql('DROP INDEX IDX_7656F53BE3F49EAB ON trip');
        $this->addSql('ALTER TABLE trip DROP active, CHANGE main_driver_id driver_id INT NOT NULL');
    }
}
