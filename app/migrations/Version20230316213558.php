<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316213558 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "order" ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE "order" DROP status');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F52993986BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F52993986BF700BD ON "order" (status_id)');
        $this->addSql('ALTER TABLE status ADD sort INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE status DROP sort');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F52993986BF700BD');
        $this->addSql('DROP INDEX IDX_F52993986BF700BD');
        $this->addSql('ALTER TABLE "order" ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" DROP status_id');
    }
}
