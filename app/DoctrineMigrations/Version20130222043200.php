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
        
        $this->addSql("ALTER TABLE acts_entities CHANGE description description LONGTEXT DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_shows_refs DROP FOREIGN KEY FK_86C0B071592D0E6F");
        $this->addSql("ALTER TABLE acts_shows_refs ADD CONSTRAINT FK_86C0B071592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_auditions DROP FOREIGN KEY FK_BFECDAF7592D0E6F");
        $this->addSql("ALTER TABLE acts_auditions ADD CONSTRAINT FK_BFECDAF7592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_techies DROP FOREIGN KEY FK_4D00DAC2592D0E6F");
        $this->addSql("ALTER TABLE acts_techies ADD CONSTRAINT FK_4D00DAC2592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE");

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

        $this->addSql("CREATE TRIGGER `entities_insert` BEFORE INSERT ON `acts_entities`
             FOR EACH ROW BEGIN
            IF NEW.slug IS NULL THEN
              SET NEW.slug = slugify(NEW.name, NEW.id);
            END IF;
            END");
        $this->addSql("CREATE TRIGGER `entities_update` BEFORE UPDATE ON `acts_entities`
             FOR EACH ROW BEGIN

            IF NEW.id <  0 THEN
                SET NEW.id = -NEW.id;
            ELSE
               IF NEW.entity_type = 'show' THEN
                   UPDATE acts_shows SET title = NEW.name, description = NEW.description, id = -NEW.id WHERE id=NEW.id;
               ELSEIF NEW.entity_type = 'person' THEN
                   UPDATE acts_people_data SET name = NEW.name, id = -NEW.id WHERE id=NEW.id;
               ELSEIF NEW.entity_type = 'venue' OR NEW.entity_type = 'society' THEN
                   UPDATE acts_societies SET name = NEW.name, description = NEW.description, id = -NEW.id WHERE id=NEW.id;
              END IF;
            END IF;
            END");
        $this->addSql("CREATE TRIGGER `shows_insert` BEFORE INSERT ON `acts_shows`
             FOR EACH ROW BEGIN
                    IF NOT NEW.id THEN
                           INSERT INTO acts_entities (name, description, entity_type) VALUES (NEW.title, NEW.description, 'show');
                           SET NEW.id = LAST_INSERT_ID();
                   END IF;
            END");
        $this->addSql("CREATE TRIGGER `shows_update` BEFORE UPDATE ON `acts_shows`
             FOR EACH ROW BEGIN

            IF NEW.id < 0 THEN
                SET NEW.id = -NEW.id;
            ELSE
               UPDATE acts_entities SET name = NEW.title, description = NEW.description, id = -NEW.id WHERE id=NEW.id;
            END IF;
            END");
        $this->addSql("CREATE TRIGGER `shows_delete` AFTER DELETE ON `acts_shows`
             FOR EACH ROW BEGIN
               DELETE FROM acts_entities WHERE id=OLD.id;
            END");
        $this->addSql("CREATE TRIGGER `people_insert` BEFORE INSERT ON `acts_people_data`
             FOR EACH ROW BEGIN
                        IF NOT NEW.id THEN
                               INSERT INTO acts_entities (name, entity_type) VALUES (NEW.name, 'person');
                               SET NEW.id = LAST_INSERT_ID();
                       END IF;
                END");
        $this->addSql("CREATE TRIGGER `people_update` BEFORE UPDATE ON `acts_people_data`
             FOR EACH ROW BEGIN

            IF NEW.id < 0 THEN
                SET NEW.id = -NEW.id;
            ELSE
               UPDATE acts_entities SET name = NEW.name, id = -NEW.id WHERE id=NEW.id;
            END IF;
            END");
        $this->addSql("CREATE TRIGGER `people_delete` AFTER DELETE ON `acts_people_data`
             FOR EACH ROW BEGIN
               DELETE FROM acts_entities WHERE id=OLD.id;
            END");
        $this->addSql("CREATE TRIGGER `societies_insert_before` BEFORE INSERT ON `acts_societies`
             FOR EACH ROW BEGIN
                        IF NOT NEW.id THEN
                               IF NEW.type = 0 THEN
                                   INSERT INTO acts_entities (name, description, entity_type) VALUES (NEW.name, NEW.description, 'society');
                               ELSE
                                   INSERT INTO acts_entities (name, description, entity_type) VALUES (NEW.name, NEW.description, 'venue');
                               END IF;
                               SET NEW.id = LAST_INSERT_ID();
                       END IF;
                END");
        $this->addSql("CREATE TRIGGER `societies_insert_after` AFTER INSERT ON `acts_societies`
             FOR EACH ROW BEGIN
                        IF NEW.type = 0 THEN
                              INSERT IGNORE INTO acts_societies_new (id) VALUES (NEW.id);
                         ELSE
                              INSERT IGNORE INTO acts_venues (id) VALUES (NEW.id);
                         END IF;
             END");
        $this->addSql("CREATE TRIGGER `societies_update` BEFORE UPDATE ON `acts_societies`
             FOR EACH ROW BEGIN

            IF NEW.id < 0 THEN
                SET NEW.id = -NEW.id;
            ELSE
               UPDATE acts_entities SET name = NEW.name, description = NEW.description, id = -NEW.id WHERE id=NEW.id;
            END IF;
            END");
        $this->addSql("CREATE TRIGGER `societies_delete` AFTER DELETE ON `acts_societies`
             FOR EACH ROW BEGIN
               DELETE FROM acts_venues WHERE id=OLD.id;
               DELETE FROM acts_societies_new WHERE id=OLD.id;
               DELETE FROM acts_entities WHERE id=OLD.id;
            END");
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
        $this->addSql("ALTER TABLE acts_entities CHANGE description description LONGTEXT NOT NULL");

        $this->addSql("DROP TRIGGER IF EXISTS `entities_insert`");
        $this->addSql("DROP TRIGGER IF EXISTS `entities_update`");
        $this->addSql("DROP TRIGGER IF EXISTS `people_insert`");
        $this->addSql("DROP TRIGGER IF EXISTS `people_update`");
        $this->addSql("DROP TRIGGER IF EXISTS `people_delete`");
        $this->addSql("DROP TRIGGER IF EXISTS `shows_insert`");
        $this->addSql("DROP TRIGGER IF EXISTS `shows_update`");
        $this->addSql("DROP TRIGGER IF EXISTS `shows_delete`");
        $this->addSql("DROP TRIGGER IF EXISTS `societies_insert_before`");
        $this->addSql("DROP TRIGGER IF EXISTS `societies_insert_after`");
        $this->addSql("DROP TRIGGER IF EXISTS `societies_update`");
        $this->addSql("DROP TRIGGER IF EXISTS `societies_delete`");
        $this->addSql("DROP FUNCTION `slugify`");
    }
}
