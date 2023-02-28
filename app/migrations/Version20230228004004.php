<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230228004004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee ADD city INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A12D5B0234 FOREIGN KEY (city) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5D9F75A12D5B0234 ON employee (city)');
        $this->addSql('ALTER TABLE passport ADD city INT DEFAULT NULL');
        $this->addSql('ALTER TABLE passport ADD CONSTRAINT FK_B5A26E082D5B0234 FOREIGN KEY (city) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B5A26E082D5B0234 ON passport (city)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE passport DROP CONSTRAINT FK_B5A26E082D5B0234');
        $this->addSql('DROP INDEX IDX_B5A26E082D5B0234');
        $this->addSql('ALTER TABLE passport DROP city');
        $this->addSql('ALTER TABLE employee DROP CONSTRAINT FK_5D9F75A12D5B0234');
        $this->addSql('DROP INDEX IDX_5D9F75A12D5B0234');
        $this->addSql('ALTER TABLE employee DROP city');
    }
}
