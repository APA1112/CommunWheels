<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240426114923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE driver_group (driver_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_ED4289E8C3423909 (driver_id), INDEX IDX_ED4289E8FE54D947 (group_id), PRIMARY KEY(driver_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE driver_group ADD CONSTRAINT FK_ED4289E8C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE driver_group ADD CONSTRAINT FK_ED4289E8FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE absence ADD driver_id INT NOT NULL');
        $this->addSql('ALTER TABLE absence ADD CONSTRAINT FK_765AE0C9C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('CREATE INDEX IDX_765AE0C9C3423909 ON absence (driver_id)');
        $this->addSql('ALTER TABLE driver DROP FOREIGN KEY FK_11667CD99A5BDCB7');
        $this->addSql('DROP INDEX IDX_11667CD99A5BDCB7 ON driver');
        $this->addSql('ALTER TABLE driver DROP absences_id');
        $this->addSql('ALTER TABLE non_school_day ADD band_id INT NOT NULL');
        $this->addSql('ALTER TABLE non_school_day ADD CONSTRAINT FK_BFD4F6BC49ABEB17 FOREIGN KEY (band_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_BFD4F6BC49ABEB17 ON non_school_day (band_id)');
        $this->addSql('ALTER TABLE schedule ADD driver_id INT NOT NULL');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBC3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('CREATE INDEX IDX_5A3811FBC3423909 ON schedule (driver_id)');
        $this->addSql('ALTER TABLE time_table ADD band_id INT NOT NULL');
        $this->addSql('ALTER TABLE time_table ADD CONSTRAINT FK_B35B6E3A49ABEB17 FOREIGN KEY (band_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_B35B6E3A49ABEB17 ON time_table (band_id)');
        $this->addSql('ALTER TABLE trip ADD time_table_id INT NOT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B487A6B90 FOREIGN KEY (time_table_id) REFERENCES time_table (id)');
        $this->addSql('CREATE INDEX IDX_7656F53B487A6B90 ON trip (time_table_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE driver_group DROP FOREIGN KEY FK_ED4289E8C3423909');
        $this->addSql('ALTER TABLE driver_group DROP FOREIGN KEY FK_ED4289E8FE54D947');
        $this->addSql('DROP TABLE driver_group');
        $this->addSql('ALTER TABLE absence DROP FOREIGN KEY FK_765AE0C9C3423909');
        $this->addSql('DROP INDEX IDX_765AE0C9C3423909 ON absence');
        $this->addSql('ALTER TABLE absence DROP driver_id');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B487A6B90');
        $this->addSql('DROP INDEX IDX_7656F53B487A6B90 ON trip');
        $this->addSql('ALTER TABLE trip DROP time_table_id');
        $this->addSql('ALTER TABLE time_table DROP FOREIGN KEY FK_B35B6E3A49ABEB17');
        $this->addSql('DROP INDEX IDX_B35B6E3A49ABEB17 ON time_table');
        $this->addSql('ALTER TABLE time_table DROP band_id');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FBC3423909');
        $this->addSql('DROP INDEX IDX_5A3811FBC3423909 ON schedule');
        $this->addSql('ALTER TABLE schedule DROP driver_id');
        $this->addSql('ALTER TABLE non_school_day DROP FOREIGN KEY FK_BFD4F6BC49ABEB17');
        $this->addSql('DROP INDEX IDX_BFD4F6BC49ABEB17 ON non_school_day');
        $this->addSql('ALTER TABLE non_school_day DROP band_id');
        $this->addSql('ALTER TABLE driver ADD absences_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE driver ADD CONSTRAINT FK_11667CD99A5BDCB7 FOREIGN KEY (absences_id) REFERENCES absence (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_11667CD99A5BDCB7 ON driver (absences_id)');
    }
}
