<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240503120441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_driver (group_id INT NOT NULL, driver_id INT NOT NULL, INDEX IDX_C2E0A409FE54D947 (group_id), INDEX IDX_C2E0A409C3423909 (driver_id), PRIMARY KEY(group_id, driver_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_driver ADD CONSTRAINT FK_C2E0A409FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_driver ADD CONSTRAINT FK_C2E0A409C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE driver_group DROP FOREIGN KEY FK_ED4289E8C3423909');
        $this->addSql('ALTER TABLE driver_group DROP FOREIGN KEY FK_ED4289E8FE54D947');
        $this->addSql('DROP TABLE driver_group');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE driver_group (driver_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_ED4289E8FE54D947 (group_id), INDEX IDX_ED4289E8C3423909 (driver_id), PRIMARY KEY(driver_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE driver_group ADD CONSTRAINT FK_ED4289E8C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE driver_group ADD CONSTRAINT FK_ED4289E8FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_driver DROP FOREIGN KEY FK_C2E0A409FE54D947');
        $this->addSql('ALTER TABLE group_driver DROP FOREIGN KEY FK_C2E0A409C3423909');
        $this->addSql('DROP TABLE group_driver');
    }
}
