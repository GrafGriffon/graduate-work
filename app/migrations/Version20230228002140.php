<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228002140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE city_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE conscript_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE employee_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE passport_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE city (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE conscript (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE employee (id INT NOT NULL, conscript INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, patronic_name VARCHAR(255) NOT NULL, floor BOOLEAN NOT NULL, phone_home VARCHAR(255) NOT NULL, phone_mobile VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, working_place VARCHAR(255) NOT NULL, position VARCHAR(255) NOT NULL, income INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5D9F75A1FCE60AF9 ON employee (conscript)');
        $this->addSql('CREATE TABLE passport (id INT NOT NULL, series VARCHAR(2) NOT NULL, number VARCHAR(14) NOT NULL, issued_by VARCHAR(255) NOT NULL, start_date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1FCE60AF9 FOREIGN KEY (conscript) REFERENCES conscript (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A1FCE60AF9');
        $this->addSql('DROP SEQUENCE city_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE conscript_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE employee_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE passport_id_seq CASCADE');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE conscript');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE passport');
    }
}
