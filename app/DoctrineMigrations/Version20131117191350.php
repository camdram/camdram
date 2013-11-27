<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131117191350 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql('UPDATE `acts_shows` SET primaryref = NULL WHERE primaryref = 0');
        $this->addSql("CREATE INDEX IDX_1A1A53FE53F0F7FF ON acts_shows (primaryref)");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE53F0F7FF FOREIGN KEY (primaryref) REFERENCES acts_shows_refs (refid) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE53F0F7FF");
        $this->addSql("DROP INDEX IDX_1A1A53FE53F0F7FF ON acts_shows");
    }
}
