<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180610123836 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_keywords DROP FOREIGN KEY FK_B2CE00DB8BF4141');
        $this->addSql('ALTER TABLE acts_knowledgebase DROP FOREIGN KEY FK_2582F9998BF4141');
        $this->addSql('ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE53F0F7FF');
        $this->addSql('DROP TABLE acts_catalogue');
        $this->addSql('DROP TABLE acts_config');
        $this->addSql('DROP TABLE acts_email');
        $this->addSql('DROP TABLE acts_email_aliases');
        $this->addSql('DROP TABLE acts_email_items');
        $this->addSql('DROP TABLE acts_email_sigs');
        $this->addSql('DROP TABLE acts_forums');
        $this->addSql('DROP TABLE acts_forums_messages');
        $this->addSql('DROP TABLE acts_includes');
        $this->addSql('DROP TABLE acts_keywords');
        $this->addSql('DROP TABLE acts_knowledgebase');
        $this->addSql('DROP TABLE acts_mailinglists');
        $this->addSql('DROP TABLE acts_mailinglists_members');
        $this->addSql('DROP TABLE acts_pages');
        $this->addSql('DROP TABLE acts_reviews');
        $this->addSql('DROP TABLE acts_search_cache');
        $this->addSql('DROP TABLE acts_shows_refs');
        $this->addSql('DROP TABLE acts_stores');
        $this->addSql('DROP TABLE acts_techies_positions');
        $this->addSql('DROP TABLE acts_termdates');
        $this->addSql('DROP INDEX UNIQ_1A1A53FE53F0F7FF ON acts_shows');
        $this->addSql('ALTER TABLE acts_shows DROP primaryref');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acts_catalogue (id INT AUTO_INCREMENT NOT NULL, storeid INT NOT NULL, description MEDIUMTEXT NOT NULL COLLATE utf8_general_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_config (name VARCHAR(255) NOT NULL COLLATE utf8_general_ci, value MEDIUMTEXT NOT NULL COLLATE utf8_general_ci, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_email (emailid INT AUTO_INCREMENT NOT NULL, userid INT DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_general_ci, public_add TINYINT(1) NOT NULL, summary LONGTEXT NOT NULL COLLATE utf8_general_ci, `from` INT NOT NULL, listid TEXT NOT NULL COLLATE utf8_general_ci, deleteonsend TINYINT(1) NOT NULL, INDEX IDX_91B3DECEF132696E (userid), PRIMARY KEY(emailid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_email_aliases (id INT AUTO_INCREMENT NOT NULL, uid INT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_general_ci, email VARCHAR(255) NOT NULL COLLATE utf8_general_ci, INDEX IDX_CFB21822539B0606 (uid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_email_items (id INT AUTO_INCREMENT NOT NULL, emailid INT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_general_ci, text LONGTEXT NOT NULL COLLATE utf8_general_ci, orderid DOUBLE PRECISION NOT NULL, creatorid INT NOT NULL, created DATETIME NOT NULL, protect TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_email_sigs (id INT AUTO_INCREMENT NOT NULL, uid INT DEFAULT NULL, sig LONGTEXT NOT NULL COLLATE utf8_general_ci, INDEX IDX_9EB1EE3E539B0606 (uid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_forums (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_general_ci, description MEDIUMTEXT NOT NULL COLLATE utf8_general_ci, orderid INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_forums_messages (id INT AUTO_INCREMENT NOT NULL, replyid INT NOT NULL, forumid INT NOT NULL, uid INT NOT NULL, datetime DATETIME NOT NULL, subject VARCHAR(255) NOT NULL COLLATE utf8_general_ci, message LONGTEXT NOT NULL COLLATE utf8_general_ci, resourceid INT NOT NULL, ancestorid INT NOT NULL, lastpost DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_includes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_general_ci, text MEDIUMTEXT NOT NULL COLLATE utf8_general_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_keywords (id INT AUTO_INCREMENT NOT NULL, pageid INT NOT NULL, kw VARCHAR(255) NOT NULL COLLATE utf8_general_ci, INDEX IDX_B2CE00DB8BF4141 (pageid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_knowledgebase (id INT AUTO_INCREMENT NOT NULL, pageid INT DEFAULT NULL, userid INT DEFAULT NULL, text LONGTEXT NOT NULL COLLATE utf8_general_ci, date DATETIME NOT NULL, INDEX IDX_2582F9998BF4141 (pageid), INDEX IDX_2582F999F132696E (userid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_mailinglists (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_general_ci, shortname VARCHAR(100) NOT NULL COLLATE utf8_general_ci, description MEDIUMTEXT NOT NULL COLLATE utf8_general_ci, public TINYINT(1) NOT NULL, defaultsubscribe TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_mailinglists_members (listid INT NOT NULL, uid INT NOT NULL, PRIMARY KEY(listid, uid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_pages (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_general_ci, parentid INT NOT NULL, sortcode INT NOT NULL, fulltitle VARCHAR(255) NOT NULL COLLATE utf8_general_ci, secure TINYINT(1) NOT NULL, micro TINYINT(1) NOT NULL, help LONGTEXT NOT NULL COLLATE utf8_general_ci, ghost TINYINT(1) NOT NULL, mode VARCHAR(50) DEFAULT NULL COLLATE utf8_general_ci, allowsubpage TINYINT(1) NOT NULL, intertitle MEDIUMTEXT NOT NULL COLLATE utf8_general_ci, knowledgebase TINYINT(1) NOT NULL, getvars MEDIUMTEXT NOT NULL COLLATE utf8_general_ci, postvars MEDIUMTEXT NOT NULL COLLATE utf8_general_ci, usepage VARCHAR(255) NOT NULL COLLATE utf8_general_ci, kbid INT NOT NULL, rssfeeds TEXT NOT NULL COLLATE utf8_general_ci, locked TINYINT(1) NOT NULL, virtual TINYINT(1) NOT NULL, param_parser TINYINT(1) NOT NULL, access_php MEDIUMTEXT NOT NULL COLLATE utf8_general_ci, subpagetemplate MEDIUMTEXT NOT NULL COLLATE utf8_general_ci, searchable TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_reviews (id INT AUTO_INCREMENT NOT NULL, uid INT DEFAULT NULL, showid INT NOT NULL, review LONGTEXT NOT NULL COLLATE utf8_general_ci, `from` VARCHAR(255) NOT NULL COLLATE utf8_general_ci, created DATETIME NOT NULL, INDEX IDX_4F8219D8592D0E6F (showid), INDEX IDX_4F8219D8539B0606 (uid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_search_cache (id INT AUTO_INCREMENT NOT NULL, keyword VARCHAR(200) NOT NULL COLLATE utf8_general_ci, text LONGTEXT NOT NULL COLLATE utf8_general_ci, type VARCHAR(255) NOT NULL COLLATE utf8_general_ci, url VARCHAR(255) NOT NULL COLLATE utf8_general_ci, sindex INT NOT NULL, obsolete TINYINT(1) NOT NULL, linkcode VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci, INDEX keyword (keyword), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_shows_refs (refid INT AUTO_INCREMENT NOT NULL, showid INT NOT NULL, ref VARCHAR(255) NOT NULL COLLATE utf8_general_ci, INDEX IDX_86C0B071592D0E6F (showid), PRIMARY KEY(refid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_stores (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_general_ci, shortname VARCHAR(100) NOT NULL COLLATE utf8_general_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_techies_positions (id INT AUTO_INCREMENT NOT NULL, position VARCHAR(255) NOT NULL COLLATE utf8_general_ci, orderid INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_termdates (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL COLLATE utf8_general_ci, friendlyname VARCHAR(100) NOT NULL COLLATE utf8_general_ci, startdate DATE NOT NULL, enddate DATE NOT NULL, firstweek INT NOT NULL, lastweek INT NOT NULL, displayweek TINYINT(1) NOT NULL, vacation VARCHAR(100) NOT NULL COLLATE utf8_general_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_email ADD CONSTRAINT FK_91B3DECEF132696E FOREIGN KEY (userid) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_email_aliases ADD CONSTRAINT FK_CFB21822539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_email_sigs ADD CONSTRAINT FK_9EB1EE3E539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_keywords ADD CONSTRAINT FK_B2CE00DB8BF4141 FOREIGN KEY (pageid) REFERENCES acts_pages (id)');
        $this->addSql('ALTER TABLE acts_knowledgebase ADD CONSTRAINT FK_2582F9998BF4141 FOREIGN KEY (pageid) REFERENCES acts_pages (id)');
        $this->addSql('ALTER TABLE acts_knowledgebase ADD CONSTRAINT FK_2582F999F132696E FOREIGN KEY (userid) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_reviews ADD CONSTRAINT FK_4F8219D8539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_reviews ADD CONSTRAINT FK_4F8219D8592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)');
        $this->addSql('ALTER TABLE acts_shows_refs ADD CONSTRAINT FK_86C0B071592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_shows ADD primaryref INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE53F0F7FF FOREIGN KEY (primaryref) REFERENCES acts_shows_refs (refid) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1A1A53FE53F0F7FF ON acts_shows (primaryref)');
    }
}
