<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131117013449 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("CREATE TABLE acts_external_users (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, service VARCHAR(50) NOT NULL, remote_id VARCHAR(100) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, profile_picture_url VARCHAR(255) DEFAULT NULL, INDEX IDX_A75DA06CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_external_users ADD CONSTRAINT FK_A75DA06CA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_people_data ADD image_id INT DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD slug VARCHAR(128) DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL");
        $this->addSql("ALTER TABLE acts_people_data ADD CONSTRAINT FK_567E1F8F3DA5256D FOREIGN KEY (image_id) REFERENCES images (id)");
        $this->addSql("CREATE INDEX IDX_567E1F8F3DA5256D ON acts_people_data (image_id)");
        $this->addSql("CREATE UNIQUE INDEX slugs ON acts_people_data (slug)");
        $this->addSql("ALTER TABLE acts_shows ADD image_id INT DEFAULT NULL, ADD facebook_id VARCHAR(50) DEFAULT NULL, ADD twitter_id VARCHAR(50) DEFAULT NULL, ADD slug VARCHAR(128) DEFAULT NULL, DROP onlinebookingurl, DROP facebookurl, DROP otherurl, CHANGE id id INT AUTO_INCREMENT NOT NULL");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE3DA5256D FOREIGN KEY (image_id) REFERENCES images (id)");
        $this->addSql("UPDATE acts_shows AS s LEFT JOIN acts_users AS u ON  s.authorizeid = u.id set s.authorizeid = NULL WHERE u.id IS NULL");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE5FB42679 FOREIGN KEY (authorizeid) REFERENCES acts_users (id)");
        $this->addSql("CREATE INDEX IDX_1A1A53FE3DA5256D ON acts_shows (image_id)");
        $this->addSql("CREATE INDEX IDX_1A1A53FE5FB42679 ON acts_shows (authorizeid)");
        $this->addSql("CREATE UNIQUE INDEX slugs ON acts_shows (slug)");
        $this->addSql("ALTER TABLE acts_auditions CHANGE endtime endtime TIME DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_societies ADD image_id INT DEFAULT NULL, ADD facebook_id VARCHAR(50) DEFAULT NULL, ADD twitter_id VARCHAR(50) DEFAULT NULL, ADD slug VARCHAR(128) DEFAULT NULL, ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE type type VARCHAR(255) NOT NULL");
        $this->addSql("ALTER TABLE acts_societies ADD CONSTRAINT FK_D8C37643DA5256D FOREIGN KEY (image_id) REFERENCES images (id)");
        $this->addSql("CREATE INDEX IDX_D8C37643DA5256D ON acts_societies (image_id)");
        $this->addSql("CREATE UNIQUE INDEX slugs ON acts_societies (slug)");
        $this->addSql("ALTER TABLE acts_techies CHANGE deadlinetime deadlinetime TIME NOT NULL");
        $this->addSql("ALTER TABLE acts_users ADD is_email_verified TINYINT(1) NOT NULL, ADD profile_picture_url VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_news DROP FOREIGN KEY FK_E030B31081257D5D");
        $this->addSql("ALTER TABLE acts_news ADD CONSTRAINT FK_E030B31081257D5D FOREIGN KEY (entity_id) REFERENCES acts_societies (id)");
        $this->addSql("DROP INDEX UNIQ_D470A3A45F37A13B ON acts_auth_codes");
        $this->addSql("DROP INDEX UNIQ_75B14F685F37A13B ON acts_access_tokens");
        $this->addSql("ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064C81B28E0");
        $this->addSql("ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064539B0606");
        $this->addSql("ALTER TABLE acts_access DROP contact, CHANGE uid uid INT DEFAULT NULL, CHANGE type type VARCHAR(20) NOT NULL, CHANGE revokedate revokedate DATE DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064C81B28E0 FOREIGN KEY (revokeid) REFERENCES acts_users (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id) ON DELETE CASCADE");
        $this->addSql("DROP INDEX UNIQ_1A3F91E75F37A13B ON acts_refresh_tokens");
        $this->addSql("CREATE TABLE acts_time_periods (id INT AUTO_INCREMENT NOT NULL, group_id INT DEFAULT NULL, short_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, long_name VARCHAR(255) NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, holiday TINYINT(1) NOT NULL, visible TINYINT(1) NOT NULL, INDEX IDX_51B4603BFE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_time_periods ADD CONSTRAINT FK_51B4603BFE54D947 FOREIGN KEY (group_id) REFERENCES acts_time_period_groups (id)");
        $this->addSql("ALTER TABLE acts_shows ADD start_at DATETIME DEFAULT NULL, ADD end_at DATETIME DEFAULT NULL");
        $this->addSql("CREATE TABLE acts_time_period_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, long_name VARCHAR(255) NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, slug VARCHAR(128) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

        $this->addSql("CREATE FUNCTION `slugify`(dirty_string varchar(200), id INT) RETURNS varchar(200) CHARSET utf8
                  DETERMINISTIC
              BEGIN
                  DECLARE x, y , z, i Int;
                  Declare temp_string, new_string VarChar(200) CHARSET utf8;
                  Declare is_allowed Bool;
                  Declare c, check_char VarChar(1);

                  set temp_string = LOWER(dirty_string);

                  Set temp_string = replace(temp_string, '&', ' and ');

                  Select temp_string Regexp('[^a-z0-9\-]+') into x;
                  If x = 1 then
                      set z = 1;
                      While z <= Char_length(temp_string) Do
                          Set c = Substring(temp_string, z, 1);
                          Set is_allowed = False;
                          If !((ascii(c) = 45) or (ascii(c) >= 48 and ascii(c) <= 57) or (ascii(c) >= 97 and ascii(c) <= 122)) Then
                              Set temp_string = Replace(temp_string, c, '-');
                          End If;
                          set z = z + 1;
                      End While;
                  End If;

                  Select temp_string Regexp(\"^-|-$|'\") into x;
                  If x = 1 Then
                      Set temp_string = Replace(temp_string, \"'\", '');
                      Set z = Char_length(temp_string);
                      Set y = Char_length(temp_string);
                      Dash_check: While z > 1 Do
                          If Strcmp(SubString(temp_string, -1, 1), '-') = 0 Then
                              Set temp_string = Substring(temp_string,1, y-1);
                              Set y = y - 1;
                          Else
                              Leave Dash_check;
                          End If;
                          Set z = z - 1;
                      End While;
                  End If;

                  Repeat
                      Select temp_string Regexp(\"--\") into x;
                      If x = 1 Then
                          Set temp_string = Replace(temp_string, \"--\", \"-\");
                      End If;
                  Until x <> 1 End Repeat;

                  If LOCATE('-', temp_string) = 1 Then
                      Set temp_string = SUBSTRING(temp_string, 2);
                  End If;

                  SET temp_string = CONCAT(temp_string, '-', SUBSTR(UUID(),1,5));

                  Return temp_string;
              END
              ");


        $this->addSql("CREATE TRIGGER `shows_insert` BEFORE INSERT ON `acts_shows`
               FOR EACH ROW BEGIN
                     IF NOT NEW.id AND NEW.slug IS NULL THEN
                         SET NEW.slug = slugify(NEW.title, NEW.id);
                     END IF;
              END");

        $this->addSql("CREATE TRIGGER `people_insert` BEFORE INSERT ON `acts_people_data`
               FOR EACH ROW BEGIN
                         IF NOT NEW.id AND NEW.slug IS NULL THEN
                               SET NEW.slug = slugify(NEW.name, NEW.id);
                         END IF;
                  END");

        $this->addSql("CREATE TRIGGER `societies_insert` BEFORE INSERT ON `acts_societies`
               FOR EACH ROW BEGIN
                  IF NOT NEW.id AND NEW.slug IS NULL THEN
                       SET NEW.slug = slugify(NEW.name, NEW.id);
                  END IF;
               END");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("DROP TABLE acts_external_users");
        $this->addSql("ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064539B0606");
        $this->addSql("ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064C81B28E0");
        $this->addSql("ALTER TABLE acts_access ADD contact TINYINT(1) NOT NULL, CHANGE uid uid INT NOT NULL, CHANGE type type VARCHAR(255) NOT NULL, CHANGE revokedate revokedate DATE NOT NULL");
        $this->addSql("ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064C81B28E0 FOREIGN KEY (revokeid) REFERENCES acts_users (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_75B14F685F37A13B ON acts_access_tokens (token)");
        $this->addSql("ALTER TABLE acts_auditions CHANGE endtime endtime TIME NOT NULL");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_D470A3A45F37A13B ON acts_auth_codes (token)");
        $this->addSql("ALTER TABLE acts_news DROP FOREIGN KEY FK_E030B31081257D5D");
        $this->addSql("ALTER TABLE acts_news ADD CONSTRAINT FK_E030B31081257D5D FOREIGN KEY (entity_id) REFERENCES acts_entities (id)");
        $this->addSql("ALTER TABLE acts_people_data DROP FOREIGN KEY FK_567E1F8F3DA5256D");
        $this->addSql("DROP INDEX IDX_567E1F8F3DA5256D ON acts_people_data");
        $this->addSql("DROP INDEX slugs ON acts_people_data");
        $this->addSql("ALTER TABLE acts_people_data DROP image_id, DROP description, DROP slug, CHANGE id id INT NOT NULL");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1A3F91E75F37A13B ON acts_refresh_tokens (token)");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE3DA5256D");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE5FB42679");
        $this->addSql("DROP INDEX IDX_1A1A53FE3DA5256D ON acts_shows");
        $this->addSql("DROP INDEX IDX_1A1A53FE5FB42679 ON acts_shows");
        $this->addSql("DROP INDEX slugs ON acts_shows");
        $this->addSql("ALTER TABLE acts_shows ADD onlinebookingurl LONGTEXT DEFAULT NULL, ADD facebookurl LONGTEXT DEFAULT NULL, ADD otherurl LONGTEXT DEFAULT NULL, DROP image_id, DROP facebook_id, DROP twitter_id, DROP slug, CHANGE id id INT NOT NULL");
        $this->addSql("ALTER TABLE acts_societies DROP FOREIGN KEY FK_D8C37643DA5256D");
        $this->addSql("DROP INDEX IDX_D8C37643DA5256D ON acts_societies");
        $this->addSql("DROP INDEX slugs ON acts_societies");
        $this->addSql("ALTER TABLE acts_societies DROP image_id, DROP facebook_id, DROP twitter_id, DROP slug, DROP latitude, DROP longitude, CHANGE id id INT NOT NULL, CHANGE type type TINYINT(1) NOT NULL");
        $this->addSql("ALTER TABLE acts_techies CHANGE deadlinetime deadlinetime VARCHAR(255) NOT NULL");
        $this->addSql("ALTER TABLE acts_users DROP is_email_verified, DROP profile_picture_url");
        $this->addSql("ALTER TABLE acts_time_periods DROP FOREIGN KEY FK_51B4603BFE54D947");
        $this->addSql("DROP TABLE acts_time_period_groups");

        $this->addSql("DROP TRIGGER IF EXISTS `people_insert`");
        $this->addSql("DROP TRIGGER IF EXISTS `shows_insert`");
        $this->addSql("DROP TRIGGER IF EXISTS `societies_insert`");
        $this->addSql("DROP FUNCTION `slugify`");

        $this->addSql("DROP TABLE acts_time_periods");
        $this->addSql("ALTER TABLE acts_shows DROP start_at, DROP end_at");
    }
}
