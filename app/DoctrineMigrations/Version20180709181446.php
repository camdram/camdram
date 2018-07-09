<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180709181446 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_users CHANGE registered registered DATE DEFAULT NULL, CHANGE login login DATE DEFAULT NULL');
        $this->addSql('UPDATE acts_users set `registered` = NULL WHERE `registered` = 0000-00-00');
        $this->addSql('UPDATE acts_users set `login` = NULL WHERE `login` = 0000-00-00');
        $this->addSql('ALTER TABLE acts_users CHANGE registered registered_at DATETIME DEFAULT NULL, CHANGE login last_login_at DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_users CHANGE registered registered DATE NOT NULL, CHANGE login login DATE NOT NULL');
    }
}
