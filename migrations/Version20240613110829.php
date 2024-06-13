<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240613110829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip ADD main_driver_id INT NOT NULL, ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BE3F49EAB FOREIGN KEY (main_driver_id) REFERENCES driver (id)');
        $this->addSql('CREATE INDEX IDX_7656F53BE3F49EAB ON trip (main_driver_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BE3F49EAB');
        $this->addSql('DROP INDEX IDX_7656F53BE3F49EAB ON trip');
        $this->addSql('ALTER TABLE trip DROP main_driver_id, DROP active');
    }
}
