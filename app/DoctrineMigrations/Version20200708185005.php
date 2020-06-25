<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200708185005 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Migrate techie_adverts and applications to adverts';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acts_adverts ADD society_id INT DEFAULT NULL, ADD venue_id INT DEFAULT NULL, ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE acts_adverts ADD CONSTRAINT FK_AA7A15A0E6389D24 FOREIGN KEY (society_id) REFERENCES acts_societies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_adverts ADD CONSTRAINT FK_AA7A15A040A73EBA FOREIGN KEY (venue_id) REFERENCES acts_venues (id) ON DELETE CASCADE');

        $this->addSql('UPDATE acts_adverts SET type="audition"');
        $this->addSql('INSERT INTO acts_adverts (show_id, title, type, summary, body, display, expires_at, contact_details, created_at, updated_at)
            SELECT s.id, CONCAT("Technical team for ", s.title), "techie", t.positions, t.techextra, 1, t.expiry, t.contact, t.lastupdated, t.lastupdated 
            FROM acts_techies AS t
            JOIN acts_shows AS s ON s.id = t.showid');
        $this->addSql('INSERT INTO acts_adverts (show_id, title, type, summary, body, display, expires_at, contact_details, created_at, updated_at)
            SELECT s.id, a.text, "application", a.furtherinfo, "", 1, ADDTIME(CONVERT(a.deadlinedate, DATETIME), a.deadlinetime), "", NOW(), NOW()
            FROM acts_applications AS a
            JOIN acts_shows AS s ON s.id = a.showid');
        $this->addSql('INSERT INTO acts_adverts (society_id, title, type, summary, body, display, expires_at, contact_details, created_at, updated_at)
            SELECT s.id, a.text, "application", a.furtherinfo, "", 1, ADDTIME(CONVERT(a.deadlinedate, DATETIME), a.deadlinetime), "", NOW(), NOW()
            FROM acts_applications AS a
            JOIN acts_societies AS s ON s.id = a.showid');
        $this->addSql('INSERT INTO acts_adverts (venue_id, title, type, summary, body, display, expires_at, contact_details, created_at, updated_at)
            SELECT v.id, a.text, "application", a.furtherinfo, "", 1, ADDTIME(CONVERT(a.deadlinedate, DATETIME), a.deadlinetime), "", NOW(), NOW()
            FROM acts_applications AS a
            JOIN acts_venues AS v ON v.id = a.showid');


        $this->addSql('DROP TABLE acts_applications');
        $this->addSql('DROP TABLE acts_techies');
        $this->addSql('CREATE INDEX IDX_AA7A15A0E6389D24 ON acts_adverts (society_id)');
        $this->addSql('CREATE INDEX IDX_AA7A15A040A73EBA ON acts_adverts (venue_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acts_applications (id INT AUTO_INCREMENT NOT NULL, showid INT DEFAULT NULL, society_id INT DEFAULT NULL, venue_id INT DEFAULT NULL, text LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, deadlinedate DATE NOT NULL, furtherinfo LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, deadlinetime TIME NOT NULL, INDEX IDX_95ED3F0F40A73EBA (venue_id), INDEX IDX_95ED3F0F592D0E6F (showid), INDEX IDX_95ED3F0FE6389D24 (society_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE acts_techies (id INT AUTO_INCREMENT NOT NULL, showid INT DEFAULT NULL, positions LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, contact LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, deadline TINYINT(1) NOT NULL, expiry DATETIME NOT NULL, display TINYINT(1) NOT NULL, remindersent TINYINT(1) NOT NULL, techextra LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, lastupdated DATETIME NOT NULL, INDEX IDX_4D00DAC2592D0E6F (showid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0F592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FAF648A81 FOREIGN KEY (society_id) REFERENCES acts_societies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FE176C6 FOREIGN KEY (venue_id) REFERENCES acts_venues (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_techies ADD CONSTRAINT FK_4D00DAC2592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_adverts DROP FOREIGN KEY FK_AA7A15A0E6389D24');
        $this->addSql('ALTER TABLE acts_adverts DROP FOREIGN KEY FK_AA7A15A040A73EBA');
        $this->addSql('DROP INDEX IDX_AA7A15A0E6389D24 ON acts_adverts');
        $this->addSql('DROP INDEX IDX_AA7A15A040A73EBA ON acts_adverts');
        $this->addSql('ALTER TABLE acts_adverts DROP society_id, DROP venue_id, DROP type');
    }
}
