<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130222043200 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
          $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

          $this->addSql("ALTER TABLE acts_shows_refs DROP FOREIGN KEY FK_86C0B071592D0E6F");
          $this->addSql("ALTER TABLE acts_shows_refs ADD CONSTRAINT FK_86C0B071592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE");
          $this->addSql("ALTER TABLE acts_auditions DROP FOREIGN KEY FK_BFECDAF7592D0E6F");
          $this->addSql("ALTER TABLE acts_auditions ADD CONSTRAINT FK_BFECDAF7592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE");
          $this->addSql("ALTER TABLE acts_techies DROP FOREIGN KEY FK_4D00DAC2592D0E6F");
          $this->addSql("ALTER TABLE acts_techies ADD CONSTRAINT FK_4D00DAC2592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE acts_shows_refs DROP FOREIGN KEY FK_86C0B071592D0E6F");
        $this->addSql("ALTER TABLE acts_shows_refs ADD CONSTRAINT FK_86C0B071592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_auditions DROP FOREIGN KEY FK_BFECDAF7592D0E6F");
        $this->addSql("ALTER TABLE acts_auditions ADD CONSTRAINT FK_BFECDAF7592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_techies DROP FOREIGN KEY FK_4D00DAC2592D0E6F");
        $this->addSql("ALTER TABLE acts_techies ADD CONSTRAINT FK_4D00DAC2592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
    }
}
