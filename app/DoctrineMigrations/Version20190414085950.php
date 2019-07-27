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
        $this->addSql('ALTER TABLE acts_news ADD venue_id INT DEFAULT NULL, CHANGE entity_id society_id INT DEFAULT NULL');
        // Correct references to venues in acts_news:
        $this->addSql('UPDATE acts_news SET venue_id = society_id, society_id = NULL ' .
                      'WHERE EXISTS (SELECT name FROM acts_venues WHERE id = acts_news.society_id)');

        $this->addSql('ALTER TABLE acts_news ADD CONSTRAINT FK_E030B310AF648A81 FOREIGN KEY (society_id) REFERENCES acts_societies (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_news ADD CONSTRAINT FK_E030B310E176C6 FOREIGN KEY (venue_id) REFERENCES acts_venues (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E030B310AF648A81 ON acts_news (society_id)');
        $this->addSql('CREATE INDEX IDX_E030B310E176C6 ON acts_news (venue_id)');

        // Update application data
        $this->addSql('ALTER TABLE acts_applications ADD venue_id INT DEFAULT NULL, CHANGE socid society_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FE176C6 FOREIGN KEY (venue_id) REFERENCES acts_venues (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_95ED3F0FE176C6 ON acts_applications (venue_id)');
        $this->addSql('UPDATE acts_applications SET venue_id = society_id, society_id = NULL ' .
                      'WHERE society_id IN (SELECT id FROM acts_venues)');

        // performances
        $this->addSql('ALTER TABLE acts_performances CHANGE venid venue_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6');
        $this->addSql('ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venue_id) REFERENCES acts_venues (id) ON DELETE SET NULL');
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

        // And now the Events table (Camdram v1 relic!)
        $this->addSql('ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7AAF648A81');
        $this->addSql('DROP INDEX IDX_78452C7AAF648A81 ON acts_events');
        $this->addSql('ALTER TABLE acts_events ADD venue_id INT DEFAULT NULL, CHANGE socid society_id INT DEFAULT NULL');
        $this->addSql('UPDATE acts_events SET venue_id = society_id, society_id = NULL ' .
                      'WHERE society_id IN (SELECT id FROM acts_venues)');
        $this->addSql('ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7AE6389D24 FOREIGN KEY (society_id) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7A40A73EBA FOREIGN KEY (venue_id) REFERENCES acts_venues (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_78452C7AE6389D24 ON acts_events (society_id)');
        $this->addSql('CREATE INDEX IDX_78452C7A40A73EBA ON acts_events (venue_id)');

        // Autogenerated
        $this->addSql('DROP INDEX log_user_lookup_idx ON ext_log_entries');
        $this->addSql('DROP INDEX log_class_lookup_idx ON ext_log_entries');
        $this->addSql('DROP INDEX log_version_lookup_idx ON ext_log_entries');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('ALTER TABLE acts_applications RENAME INDEX idx_95ed3f0faf648a81 TO IDX_95ED3F0FE6389D24');
        $this->addSql('ALTER TABLE acts_applications RENAME INDEX idx_95ed3f0fe176c6 TO IDX_95ED3F0F40A73EBA');
        $this->addSql('ALTER TABLE acts_performances RENAME INDEX idx_e317f2d4e176c6 TO IDX_E317F2D440A73EBA');
        $this->addSql('ALTER TABLE acts_news RENAME INDEX idx_e030b310af648a81 TO IDX_E030B310E6389D24');
        $this->addSql('ALTER TABLE acts_news RENAME INDEX idx_e030b310e176c6 TO IDX_E030B31040A73EBA');
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
        $this->addSql('ALTER TABLE acts_applications DROP venue_id');
        $this->addSql('ALTER TABLE acts_news DROP FOREIGN KEY FK_E030B310AF648A81');
        $this->addSql('DROP INDEX IDX_E030B310AF648A81 ON acts_news');
        $this->addSql('DROP INDEX IDX_E030B310E176C6 ON acts_news');
        $this->addSql('ALTER TABLE acts_news ADD entity_id INT DEFAULT NULL, DROP society_id, DROP venue_id');
        $this->addSql('ALTER TABLE acts_news ADD CONSTRAINT FK_E030B31081257D5D FOREIGN KEY (entity_id) REFERENCES acts_societies (id)');
        $this->addSql('CREATE INDEX IDX_E030B31081257D5D ON acts_news (entity_id)');
        $this->addSql('ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6');
        $this->addSql('ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venue_id) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_performances CHANGE venue_id venid INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_shows ADD venid INT DEFAULT NULL, ADD venue VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEE176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1A1A53FEE176C6 ON acts_shows (venid)');
        $this->addSql('ALTER TABLE acts_societies ADD expires DATE DEFAULT NULL, ADD type VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD address LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7AE6389D24');
        $this->addSql('ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7A40A73EBA');
        $this->addSql('DROP INDEX IDX_78452C7AE6389D24 ON acts_events');
        $this->addSql('DROP INDEX IDX_78452C7A40A73EBA ON acts_events');
        $this->addSql('ALTER TABLE acts_events ADD socid INT DEFAULT NULL, DROP society_id, DROP venue_id');
        $this->addSql('ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7AAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_78452C7AAF648A81 ON acts_events (socid)');
        $this->addSql('ALTER TABLE acts_applications RENAME INDEX idx_95ed3f0f40a73eba TO IDX_95ED3F0FE176C6');
        $this->addSql('ALTER TABLE acts_applications RENAME INDEX idx_95ed3f0fe6389d24 TO IDX_95ED3F0FAF648A81');
        $this->addSql('ALTER TABLE acts_news RENAME INDEX idx_e030b310e6389d24 TO IDX_E030B310AF648A81');
        $this->addSql('ALTER TABLE acts_news RENAME INDEX idx_e030b31040a73eba TO IDX_E030B310E176C6');
        $this->addSql('ALTER TABLE acts_performances RENAME INDEX idx_e317f2d440a73eba TO IDX_E317F2D4E176C6');
        $this->addSql('DROP INDEX log_class_lookup_idx ON ext_log_entries');
        $this->addSql('DROP INDEX log_user_lookup_idx ON ext_log_entries');
        $this->addSql('DROP INDEX log_version_lookup_idx ON ext_log_entries');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class(191))');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username(191))');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class(191), version)');
    }
}
