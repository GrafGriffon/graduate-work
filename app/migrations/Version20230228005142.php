<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228005142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE citizenship_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE disability_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE family_status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE citizenship (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE disability (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE family_status (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE employee ADD disability INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD citizenship INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD date_of_birth DATE NOT NULL');
        $this->addSql('ALTER TABLE employee ADD place_of_birth VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE employee ADD adress VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE employee ADD familyStatus INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1413A5CB2 FOREIGN KEY (disability) REFERENCES disability (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1FFABBE FOREIGN KEY (citizenship) REFERENCES citizenship (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1CAE31FC5 FOREIGN KEY (familyStatus) REFERENCES family_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5D9F75A1413A5CB2 ON employee (disability)');
        $this->addSql('CREATE INDEX IDX_5D9F75A1FFABBE ON employee (citizenship)');
        $this->addSql('CREATE INDEX IDX_5D9F75A1CAE31FC5 ON employee (familyStatus)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A1FFABBE');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A1413A5CB2');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A1CAE31FC5');
        $this->addSql('DROP SEQUENCE citizenship_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE disability_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE family_status_id_seq CASCADE');
        $this->addSql('DROP TABLE citizenship');
        $this->addSql('DROP TABLE disability');
        $this->addSql('DROP TABLE family_status');
        $this->addSql('DROP INDEX IDX_5D9F75A1413A5CB2');
        $this->addSql('DROP INDEX IDX_5D9F75A1FFABBE');
        $this->addSql('DROP INDEX IDX_5D9F75A1CAE31FC5');
        $this->addSql('ALTER TABLE employee DROP disability');
        $this->addSql('ALTER TABLE employee DROP citizenship');
        $this->addSql('ALTER TABLE employee DROP date_of_birth');
        $this->addSql('ALTER TABLE employee DROP place_of_birth');
        $this->addSql('ALTER TABLE employee DROP adress');
        $this->addSql('ALTER TABLE employee DROP familyStatus');
    }
}
