<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150330003551 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acts_email_bounces (id INT AUTO_INCREMENT NOT NULL, from_header VARCHAR(255) NOT NULL, to_header VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, received_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_support ADD CONSTRAINT FK_A6F619725B919408 FOREIGN KEY (supportid) REFERENCES acts_support (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE acts_email_bounces');
        $this->addSql('ALTER TABLE acts_support DROP FOREIGN KEY FK_A6F619725B919408');
    }
}
