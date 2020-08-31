<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200831092852 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // Create Event–Society join table
        $this->addSql('CREATE TABLE acts_event_soc_link (event_id INT NOT NULL, society_id INT NOT NULL, INDEX IDX_DEB789C371F7E88B (event_id), INDEX IDX_DEB789C3E6389D24 (society_id), PRIMARY KEY(event_id, society_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_event_soc_link ADD CONSTRAINT FK_DEB789C371F7E88B FOREIGN KEY (event_id) REFERENCES acts_events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_event_soc_link ADD CONSTRAINT FK_DEB789C3E6389D24 FOREIGN KEY (society_id) REFERENCES acts_societies (id) ON DELETE CASCADE');
        // Populate it
        $this->addSql('INSERT INTO acts_event_soc_link (event_id, society_id) SELECT id, socid FROM acts_events WHERE socid IS NOT NULL');

        // Modify Event table
        $this->addSql('DELETE FROM acts_events WHERE date IS NULL');
        $this->addSql('ALTER TABLE acts_events ADD socs_list LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', ADD start_at DATETIME NOT NULL, CHANGE linkid linkid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7AAF648A81');
        $this->addSql('DROP INDEX IDX_78452C7AAF648A81 ON acts_events');
        // Transfer data to new columns
        $this->addSql('UPDATE acts_events SET socs_list = CONCAT(\'[\', socid, \']\') WHERE socid IS NOT NULL');
        $this->addSql('UPDATE acts_events SET socs_list = \'[]\' WHERE socid IS NULL');
        $this->addSql('UPDATE acts_events SET start_at = TIMESTAMP(date, starttime)');
        $this->addSql('UPDATE acts_events SET linkid = NULL WHERE linkid NOT IN (SELECT id FROM (SELECT id FROM acts_events) AS err1093_workaround)');
        // Drop old columns and finish Event table
        $this->addSql('ALTER TABLE acts_events DROP socid, DROP date, DROP starttime');
        $this->addSql('ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7A3B4DAAE0 FOREIGN KEY (linkid) REFERENCES acts_events (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_78452C7A3B4DAAE0 ON acts_events (linkid)');
        $this->addSql('CREATE FULLTEXT INDEX idx_event_fulltext ON acts_events (text)');
        $this->addSql('ALTER TABLE acts_events ADD colour VARCHAR(7) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acts_events DROP colour');
        $this->addSql('DROP INDEX idx_event_fulltext ON acts_events');
        $this->addSql('DROP TABLE acts_event_soc_link');
        $this->addSql('ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7A3B4DAAE0');
        $this->addSql('DROP INDEX IDX_78452C7A3B4DAAE0 ON acts_events');
        $this->addSql('ALTER TABLE acts_events ADD socid INT DEFAULT NULL, ADD date DATE NOT NULL, DROP socs_list, CHANGE linkid linkid INT NOT NULL, CHANGE start_at starttime TIME NOT NULL');
        $this->addSql('ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7AAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_78452C7AAF648A81 ON acts_events (socid)');
    }
}
