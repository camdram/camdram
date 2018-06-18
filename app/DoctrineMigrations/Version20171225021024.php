<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171225021024 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acts_support2 (id INT AUTO_INCREMENT NOT NULL, ownerid INT DEFAULT NULL, supportid INT DEFAULT NULL, `from` VARCHAR(255) NOT NULL, `to` VARCHAR(255) NOT NULL, cc VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, state VARCHAR(20) NOT NULL, datetime DATETIME NOT NULL, INDEX IDX_A478580475DAD987 (ownerid), INDEX IDX_A47858045B919408 (supportid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_support2 ADD CONSTRAINT FK_A478580475DAD987 FOREIGN KEY (ownerid) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_support2 ADD CONSTRAINT FK_A47858045B919408 FOREIGN KEY (supportid) REFERENCES acts_support2 (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE acts_support2');
    }
}
