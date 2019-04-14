<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190414085950 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acts_venues (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, address LONGTEXT DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, facebook_id VARCHAR(50) DEFAULT NULL, twitter_id VARCHAR(50) DEFAULT NULL, shortname VARCHAR(100) DEFAULT NULL, college VARCHAR(100) DEFAULT NULL, logourl VARCHAR(255) DEFAULT NULL, slug VARCHAR(128) NOT NULL, INDEX IDX_4EEC599D3DA5256D (image_id), UNIQUE INDEX ven_slugs (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_venues ADD CONSTRAINT FK_4EEC599D3DA5256D FOREIGN KEY (image_id) REFERENCES images (id)');
        // Transfer venues to a new table:
        $this->addSql('INSERT INTO acts_venues (id, image_id, address, latitude, longitude, name, description, facebook_id, twitter_id, shortname, college, logourl, slug) ' .
                      'SELECT id, image_id, address, latitude, longitude, name, description, facebook_id, twitter_id, shortname, college, logourl, slug ' .
                      'FROM acts_societies WHERE acts_societies.type = 1');

        $this->addSql('ALTER TABLE acts_news DROP FOREIGN KEY FK_E030B31081257D5D');
        $this->addSql('DROP INDEX IDX_E030B31081257D5D ON acts_news');
        $this->addSql('ALTER TABLE acts_news ADD venid INT DEFAULT NULL, CHANGE entity_id socid INT DEFAULT NULL');
        // Correct references to venues in acts_news:
        $this->addSql('UPDATE acts_news SET venid = socid, socid = NULL ' .
                      'WHERE EXISTS (SELECT name FROM acts_venues WHERE id = acts_news.socid)');

        $this->addSql('ALTER TABLE acts_news ADD CONSTRAINT FK_E030B310AF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_news ADD CONSTRAINT FK_E030B310E176C6 FOREIGN KEY (venid) REFERENCES acts_venues (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E030B310AF648A81 ON acts_news (socid)');
        $this->addSql('CREATE INDEX IDX_E030B310E176C6 ON acts_news (venid)');

        // Update application data
        $this->addSql('ALTER TABLE acts_applications ADD venid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FE176C6 FOREIGN KEY (venid) REFERENCES acts_venues (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_95ED3F0FE176C6 ON acts_applications (venid)');
        $this->addSql('UPDATE acts_applications SET venid = socid, socid = NULL ' .
                      'WHERE socid IN (SELECT id FROM acts_venues)');

        // performances
        $this->addSql('ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6');
        $this->addSql('ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_venues (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEE176C6');
        $this->addSql('DROP INDEX IDX_1A1A53FEE176C6 ON acts_shows');
        $this->addSql('ALTER TABLE acts_shows DROP venid, DROP venue');

        // Literally just drop this, it's unused and unimplemented.
        $this->addSql('ALTER TABLE acts_api_apps DROP FOREIGN KEY FK_297ABD2C9E6B1585');
        $this->addSql('DROP INDEX IDX_297ABD2C9E6B1585 ON acts_api_apps');
        $this->addSql('ALTER TABLE acts_api_apps DROP organisation_id');

        // Transfer ACEs from society to venue.
        $this->addSql("UPDATE acts_access SET type = 'venue' WHERE type = 'society' AND entity_id IN (SELECT id FROM acts_venues)");

        // Finally delete venues from acts_societies.
        $this->addSql('DELETE FROM acts_societies WHERE acts_societies.type = 1');
        $this->addSql('ALTER TABLE acts_societies DROP expires, DROP type, DROP address, DROP latitude, DROP longitude');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_news DROP FOREIGN KEY FK_E030B310E176C6');
        $this->addSql('ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0FE176C6');
        $this->addSql('ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6');
        $this->addSql('DROP TABLE acts_venues');
        $this->addSql('ALTER TABLE acts_api_apps ADD organisation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_api_apps ADD CONSTRAINT FK_297ABD2C9E6B1585 FOREIGN KEY (organisation_id) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_297ABD2C9E6B1585 ON acts_api_apps (organisation_id)');
        $this->addSql('DROP INDEX IDX_95ED3F0FE176C6 ON acts_applications');
        $this->addSql('ALTER TABLE acts_applications DROP venid');
        $this->addSql('ALTER TABLE acts_news DROP FOREIGN KEY FK_E030B310AF648A81');
        $this->addSql('DROP INDEX IDX_E030B310AF648A81 ON acts_news');
        $this->addSql('DROP INDEX IDX_E030B310E176C6 ON acts_news');
        $this->addSql('ALTER TABLE acts_news ADD entity_id INT DEFAULT NULL, DROP socid, DROP venid');
        $this->addSql('ALTER TABLE acts_news ADD CONSTRAINT FK_E030B31081257D5D FOREIGN KEY (entity_id) REFERENCES acts_societies (id)');
        $this->addSql('CREATE INDEX IDX_E030B31081257D5D ON acts_news (entity_id)');
        $this->addSql('ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6');
        $this->addSql('ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_shows ADD venid INT DEFAULT NULL, ADD venue VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEE176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1A1A53FEE176C6 ON acts_shows (venid)');
        $this->addSql('ALTER TABLE acts_societies ADD expires DATE DEFAULT NULL, ADD type VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD address LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL');
    }
}
