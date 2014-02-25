<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140225203354 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D457167AB4");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D457167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_societies CHANGE expires expires DATE DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0F592D0E6F");
        $this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0F592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_pendingaccess ADD CONSTRAINT FK_3EA48E146EEF703F FOREIGN KEY (issuerid) REFERENCES acts_users (id)");
        $this->addSql("CREATE INDEX IDX_3EA48E146EEF703F ON acts_pendingaccess (issuerid)");
        $this->addSql("ALTER TABLE acts_reviews CHANGE uid uid INT DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_email_sigs CHANGE uid uid INT DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0F592D0E6F");
        $this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0F592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_email_sigs CHANGE uid uid INT NOT NULL");
        $this->addSql("ALTER TABLE acts_pendingaccess DROP FOREIGN KEY FK_3EA48E146EEF703F");
        $this->addSql("DROP INDEX IDX_3EA48E146EEF703F ON acts_pendingaccess");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D457167AB4");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D457167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_reviews CHANGE uid uid INT NOT NULL");
        $this->addSql("ALTER TABLE acts_societies CHANGE expires expires DATE NOT NULL");
    }
}
