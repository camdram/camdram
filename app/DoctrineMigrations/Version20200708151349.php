<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200708151349 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acts_adverts (id INT AUTO_INCREMENT NOT NULL, show_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL, display TINYINT(1) NOT NULL, summary LONGTEXT NOT NULL, body LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, contact_details VARCHAR(255) NOT NULL, INDEX IDX_AA7A15A0D0C1FC64 (show_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_adverts ADD CONSTRAINT FK_AA7A15A0D0C1FC64 FOREIGN KEY (show_id) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_auditions DROP FOREIGN KEY FK_BFECDAF7592D0E6F');
        $this->addSql('DROP INDEX IDX_BFECDAF7592D0E6F ON acts_auditions');

        $this->addSql('DELETE FROM acts_auditions WHERE showid IS NULL');
        $this->addSql('INSERT INTO acts_adverts (id, show_id, title, summary, body, display, expires_at, contact_details, created_at, updated_at)
            SELECT showid, showid, CONCAT("Auditions for ", title), COALESCE(audextra, ""), "", 1, MAX(end_at), "", `timestamp`, `timestamp`
            FROM acts_auditions AS a JOiN acts_shows AS s ON a.showid = s.id GROUP BY showid');
        $this->addSql('UPDATE acts_adverts AS v INNER JOIN acts_auditions AS a ON a.showid = v.id
                            SET contact_details = a.location WHERE a.nonscheduled = 1');
        $this->addSql('DELETE FROM acts_auditions WHERE nonscheduled = 1');
    
        $this->addSql('ALTER TABLE acts_auditions DROP display, DROP nonscheduled, CHANGE showid advert_id INT NOT NULL');
        $this->addSql('ALTER TABLE acts_auditions ADD CONSTRAINT FK_BFECDAF7D07ECCB6 FOREIGN KEY (advert_id) REFERENCES acts_adverts (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_BFECDAF7D07ECCB6 ON acts_auditions (advert_id)');
        $this->addSql('ALTER TABLE acts_shows DROP techsend, DROP actorsend, DROP audextra, DROP entryexpiry');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acts_auditions DROP FOREIGN KEY FK_BFECDAF7D07ECCB6');
        $this->addSql('DROP TABLE acts_adverts');
        $this->addSql('DROP INDEX IDX_BFECDAF7D07ECCB6 ON acts_auditions');
        $this->addSql('ALTER TABLE acts_auditions ADD display TINYINT(1) NOT NULL, ADD nonscheduled TINYINT(1) NOT NULL, CHANGE advert_id showid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_auditions ADD CONSTRAINT FK_BFECDAF7592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_BFECDAF7592D0E6F ON acts_auditions (showid)');
        $this->addSql('ALTER TABLE acts_shows ADD techsend TINYINT(1) NOT NULL, ADD actorsend TINYINT(1) NOT NULL, ADD audextra LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD entryexpiry DATE NOT NULL');
    }
}
