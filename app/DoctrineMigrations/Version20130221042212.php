<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130221042212 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, hash VARCHAR(40) NOT NULL, created_at DATETIME NOT NULL, width INT NOT NULL, height INT NOT NULL, extension VARCHAR(5) NOT NULL, type VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_E01FBE6AD1B862B8 (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_entities ADD image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_entities ADD CONSTRAINT FK_487DD1603DA5256D FOREIGN KEY (image_id) REFERENCES images (id)");
        $this->addSql("CREATE INDEX IDX_487DD1603DA5256D ON acts_entities (image_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE acts_entities DROP FOREIGN KEY FK_487DD1603DA5256D");
        $this->addSql("DROP TABLE images");
        $this->addSql("DROP INDEX IDX_487DD1603DA5256D ON acts_entities");
        $this->addSql("ALTER TABLE acts_entities DROP image_id");
    }
}
