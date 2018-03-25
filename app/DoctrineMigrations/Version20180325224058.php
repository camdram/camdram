<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180325224058 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_external_users DROP remote_id');
        $this->addSql('ALTER TABLE acts_users DROP contact, DROP alumni, DROP publishemail, DROP forumnotify, DROP hearabout, DROP occupation, DROP graduation, DROP tel, DROP dbemail, DROP dbphone, DROP threadmessages, DROP reversetime, DROP resetcode');
        $this->addSql('ALTER TABLE acts_authtokens ADD CONSTRAINT FK_11BF9FFBFCF7805F FOREIGN KEY (siteid) REFERENCES acts_externalsites (id)');
        $this->addSql('CREATE INDEX IDX_11BF9FFBFCF7805F ON acts_authtokens (siteid)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_authtokens DROP FOREIGN KEY FK_11BF9FFBFCF7805F');
        $this->addSql('DROP INDEX IDX_11BF9FFBFCF7805F ON acts_authtokens');
        $this->addSql('ALTER TABLE acts_external_users ADD remote_id VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE acts_users ADD contact TINYINT(1) NOT NULL, ADD alumni TINYINT(1) NOT NULL, ADD publishemail TINYINT(1) NOT NULL, ADD forumnotify TINYINT(1) DEFAULT NULL, ADD hearabout TEXT NOT NULL COLLATE utf8_general_ci, ADD occupation VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci, ADD graduation VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci, ADD tel VARCHAR(50) DEFAULT NULL COLLATE utf8_general_ci, ADD dbemail TINYINT(1) DEFAULT NULL, ADD dbphone TINYINT(1) DEFAULT NULL, ADD threadmessages TINYINT(1) DEFAULT NULL, ADD reversetime TINYINT(1) NOT NULL, ADD resetcode VARCHAR(32) DEFAULT NULL COLLATE utf8_general_ci');
    }
}
