<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228014042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE passport_id_seq CASCADE');
        $this->addSql('DROP TABLE passport');
        $this->addSql('ALTER TABLE employee ADD passport_series VARCHAR(2) NOT NULL');
        $this->addSql('ALTER TABLE employee ADD passport_number VARCHAR(14) NOT NULL');
        $this->addSql('ALTER TABLE employee ADD passport_issued_by VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE employee ADD passport_start_date DATE NOT NULL');
        $this->addSql('ALTER TABLE employee ADD passportCity INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A12DAEFAA7 FOREIGN KEY (passportCity) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5D9F75A12DAEFAA7 ON employee (passportCity)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE passport_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE passport (id INT NOT NULL, city INT DEFAULT NULL, series VARCHAR(2) NOT NULL, number VARCHAR(14) NOT NULL, issued_by VARCHAR(255) NOT NULL, start_date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_b5a26e082d5b0234 ON passport (city)');
        $this->addSql('ALTER TABLE passport ADD CONSTRAINT fk_b5a26e082d5b0234 FOREIGN KEY (city) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A12DAEFAA7');
        $this->addSql('DROP INDEX IDX_5D9F75A12DAEFAA7');
        $this->addSql('ALTER TABLE employee DROP passport_series');
        $this->addSql('ALTER TABLE employee DROP passport_number');
        $this->addSql('ALTER TABLE employee DROP passport_issued_by');
        $this->addSql('ALTER TABLE employee DROP passport_start_date');
        $this->addSql('ALTER TABLE employee DROP passportCity');
    }
}
