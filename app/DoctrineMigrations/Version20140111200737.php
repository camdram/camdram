<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140111200737 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("CREATE TABLE acts_people_data (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, slug VARCHAR(128) DEFAULT NULL, mapto INT NOT NULL, norobots TINYINT(1) NOT NULL, INDEX IDX_567E1F8F3DA5256D (image_id), UNIQUE INDEX people_slugs (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_shows (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, socid INT DEFAULT NULL, venid INT DEFAULT NULL, authorizeid INT DEFAULT NULL, primaryref INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, facebook_id VARCHAR(50) DEFAULT NULL, twitter_id VARCHAR(50) DEFAULT NULL, slug VARCHAR(128) DEFAULT NULL, dates VARCHAR(255) NOT NULL, author VARCHAR(255) DEFAULT NULL, prices VARCHAR(255) DEFAULT NULL, photourl LONGTEXT DEFAULT NULL, venue VARCHAR(255) DEFAULT NULL, excludedate DATE DEFAULT NULL, society VARCHAR(255) DEFAULT NULL, techsend TINYINT(1) NOT NULL, actorsend TINYINT(1) NOT NULL, audextra LONGTEXT DEFAULT NULL, entered TINYINT(1) NOT NULL, entryexpiry DATE NOT NULL, category VARCHAR(255) NOT NULL, bookingcode VARCHAR(255) DEFAULT NULL, timestamp DATETIME NOT NULL, start_at DATETIME DEFAULT NULL, end_at DATETIME DEFAULT NULL, freebase_id VARCHAR(255) DEFAULT NULL, facebookurl VARCHAR(2083) DEFAULT NULL, otherurl VARCHAR(2083) DEFAULT NULL, onlinebookingurl VARCHAR(2083) DEFAULT NULL, INDEX IDX_1A1A53FE3DA5256D (image_id), INDEX IDX_1A1A53FEAF648A81 (socid), INDEX IDX_1A1A53FEE176C6 (venid), INDEX IDX_1A1A53FE5FB42679 (authorizeid), UNIQUE INDEX UNIQ_1A1A53FE53F0F7FF (primaryref), UNIQUE INDEX show_slugs (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_name_aliases (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_355DA778217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_performances (id INT AUTO_INCREMENT NOT NULL, sid INT DEFAULT NULL, venid INT DEFAULT NULL, startdate DATE NOT NULL, enddate DATE NOT NULL, excludedate DATE DEFAULT NULL, time TIME NOT NULL, venue VARCHAR(255) DEFAULT NULL, INDEX IDX_E317F2D457167AB4 (sid), INDEX IDX_E317F2D4E176C6 (venid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_auditions (id INT AUTO_INCREMENT NOT NULL, showid INT DEFAULT NULL, date DATE NOT NULL, starttime TIME NOT NULL, endtime TIME DEFAULT NULL, location VARCHAR(255) NOT NULL, display TINYINT(1) NOT NULL, nonscheduled TINYINT(1) NOT NULL, INDEX IDX_BFECDAF7592D0E6F (showid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_societies (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, facebook_id VARCHAR(50) DEFAULT NULL, twitter_id VARCHAR(50) DEFAULT NULL, shortname VARCHAR(100) NOT NULL, college VARCHAR(100) DEFAULT NULL, affiliate TINYINT(1) NOT NULL, logourl VARCHAR(255) DEFAULT NULL, slug VARCHAR(128) DEFAULT NULL, expires DATE DEFAULT NULL, type VARCHAR(255) NOT NULL, address LONGTEXT DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, INDEX IDX_D8C37643DA5256D (image_id), UNIQUE INDEX org_slugs (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_techies (id INT AUTO_INCREMENT NOT NULL, showid INT DEFAULT NULL, positions LONGTEXT NOT NULL, contact LONGTEXT NOT NULL, deadline TINYINT(1) NOT NULL, deadlinetime TIME NOT NULL, expiry DATE NOT NULL, display TINYINT(1) NOT NULL, remindersent TINYINT(1) NOT NULL, techextra LONGTEXT NOT NULL, lastupdated DATETIME NOT NULL, INDEX IDX_4D00DAC2592D0E6F (showid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_users (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, pass VARCHAR(32) DEFAULT NULL, registered DATE NOT NULL, login DATE NOT NULL, contact TINYINT(1) NOT NULL, alumni TINYINT(1) NOT NULL, publishemail TINYINT(1) NOT NULL, hearabout LONGTEXT NOT NULL, occupation VARCHAR(255) DEFAULT NULL, graduation VARCHAR(255) DEFAULT NULL, tel VARCHAR(50) DEFAULT NULL, dbemail TINYINT(1) DEFAULT NULL, dbphone TINYINT(1) DEFAULT NULL, forumnotify TINYINT(1) DEFAULT NULL, threadmessages TINYINT(1) DEFAULT NULL, reversetime TINYINT(1) NOT NULL, resetcode VARCHAR(32) DEFAULT NULL, upgraded_at DATETIME DEFAULT NULL, is_email_verified TINYINT(1) NOT NULL, profile_picture_url VARCHAR(255) DEFAULT NULL, INDEX IDX_62A20753217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_support (id INT AUTO_INCREMENT NOT NULL, ownerid INT DEFAULT NULL, supportid INT NOT NULL, `from` VARCHAR(255) NOT NULL, `to` VARCHAR(255) NOT NULL, cc VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, state VARCHAR(20) NOT NULL, datetime DATETIME NOT NULL, INDEX IDX_A6F6197275DAD987 (ownerid), INDEX IDX_A6F619725B919408 (supportid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_events (id INT AUTO_INCREMENT NOT NULL, socid INT DEFAULT NULL, text VARCHAR(255) NOT NULL, endtime TIME NOT NULL, starttime TIME NOT NULL, date DATE NOT NULL, description LONGTEXT NOT NULL, linkid INT NOT NULL, INDEX IDX_78452C7AAF648A81 (socid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_week_names (id INT AUTO_INCREMENT NOT NULL, time_period_id INT DEFAULT NULL, short_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(128) DEFAULT NULL, start_at DATETIME NOT NULL, INDEX IDX_C467B35F7EFD7106 (time_period_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_time_periods (id INT AUTO_INCREMENT NOT NULL, short_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, slug VARCHAR(128) DEFAULT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_shows_people_link (id INT AUTO_INCREMENT NOT NULL, sid INT DEFAULT NULL, pid INT DEFAULT NULL, type VARCHAR(20) NOT NULL, role VARCHAR(255) NOT NULL, `order` INT NOT NULL, INDEX IDX_2F5AB85E57167AB4 (sid), INDEX IDX_2F5AB85E5550C4ED (pid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_news_mentions (id INT AUTO_INCREMENT NOT NULL, news_id INT DEFAULT NULL, entity_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, remote_id VARCHAR(255) NOT NULL, service VARCHAR(20) NOT NULL, offset INT NOT NULL, length INT NOT NULL, INDEX IDX_9A671BDAB5A459A0 (news_id), INDEX IDX_9A671BDA81257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_news (id INT AUTO_INCREMENT NOT NULL, entity_id INT DEFAULT NULL, remote_id VARCHAR(255) DEFAULT NULL, source VARCHAR(20) NOT NULL, picture VARCHAR(255) DEFAULT NULL, body LONGTEXT NOT NULL, num_comments INT DEFAULT NULL, num_likes INT DEFAULT NULL, public TINYINT(1) NOT NULL, posted_at DATETIME NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_E030B31081257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_applications (id INT AUTO_INCREMENT NOT NULL, showid INT DEFAULT NULL, socid INT DEFAULT NULL, text LONGTEXT NOT NULL, deadlinedate DATE NOT NULL, furtherinfo LONGTEXT NOT NULL, deadlinetime TIME NOT NULL, INDEX IDX_95ED3F0F592D0E6F (showid), INDEX IDX_95ED3F0FAF648A81 (socid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_pendingaccess (id INT AUTO_INCREMENT NOT NULL, rid INT NOT NULL, email VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, issuerid INT NOT NULL, creationdate DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_news_links (id INT AUTO_INCREMENT NOT NULL, news_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, link VARCHAR(255) NOT NULL, source VARCHAR(255) DEFAULT NULL, media_type VARCHAR(20) DEFAULT NULL, description LONGTEXT DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, INDEX IDX_56C215DAB5A459A0 (news_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_external_users (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, person_id INT DEFAULT NULL, service VARCHAR(50) NOT NULL, remote_id VARCHAR(100) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, profile_picture_url VARCHAR(255) DEFAULT NULL, INDEX IDX_A75DA06CA76ED395 (user_id), INDEX IDX_A75DA06C217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_similar_names (id INT AUTO_INCREMENT NOT NULL, name1 VARCHAR(255) NOT NULL, name2 VARCHAR(255) NOT NULL, equivalence TINYINT(1) NOT NULL, UNIQUE INDEX names_unique (name1, name2), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_access (id INT AUTO_INCREMENT NOT NULL, uid INT DEFAULT NULL, issuerid INT DEFAULT NULL, revokeid INT DEFAULT NULL, rid INT NOT NULL, type VARCHAR(20) NOT NULL, creationdate DATE NOT NULL, revokedate DATE DEFAULT NULL, contact TINYINT(1) NOT NULL, INDEX IDX_2DAB5064539B0606 (uid), INDEX IDX_2DAB50646EEF703F (issuerid), INDEX IDX_2DAB5064C81B28E0 (revokeid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, hash VARCHAR(40) NOT NULL, created_at DATETIME NOT NULL, width INT NOT NULL, height INT NOT NULL, extension VARCHAR(5) NOT NULL, type VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_E01FBE6AD1B862B8 (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_api_auth_codes (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_6FB69A2F5F37A13B (token), INDEX IDX_6FB69A2F19EB6921 (client_id), INDEX IDX_6FB69A2FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_api_apps (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, organisation_id INT DEFAULT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris LONGTEXT NOT NULL COMMENT '(DC2Type:array)', secret VARCHAR(255) NOT NULL, allowed_grant_types LONGTEXT NOT NULL COMMENT '(DC2Type:array)', name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, app_type VARCHAR(20) NOT NULL, is_admin TINYINT(1) NOT NULL, website VARCHAR(1024) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_297ABD2CA76ED395 (user_id), INDEX IDX_297ABD2C9E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_api_authorisations (externalapp_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_EF3B9B55C1F2BE32 (externalapp_id), INDEX IDX_EF3B9B55A76ED395 (user_id), PRIMARY KEY(externalapp_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_api_access_tokens (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E452518D5F37A13B (token), INDEX IDX_E452518D19EB6921 (client_id), INDEX IDX_E452518DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_api_refresh_tokens (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_CACE640E5F37A13B (token), INDEX IDX_CACE640E19EB6921 (client_id), INDEX IDX_CACE640EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_mailinglists (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, shortname VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, public TINYINT(1) NOT NULL, defaultsubscribe TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_email_aliases (id INT AUTO_INCREMENT NOT NULL, uid INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_CFB21822539B0606 (uid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_forums_messages (id INT AUTO_INCREMENT NOT NULL, replyid INT NOT NULL, forumid INT NOT NULL, uid INT NOT NULL, datetime DATETIME NOT NULL, subject VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, resourceid INT NOT NULL, ancestorid INT NOT NULL, lastpost DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_email_items (id INT AUTO_INCREMENT NOT NULL, emailid INT NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, orderid DOUBLE PRECISION NOT NULL, creatorid INT NOT NULL, created DATETIME NOT NULL, protect TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_knowledgebase (id INT AUTO_INCREMENT NOT NULL, pageid INT DEFAULT NULL, userid INT DEFAULT NULL, text LONGTEXT NOT NULL, date DATETIME NOT NULL, INDEX IDX_2582F9998BF4141 (pageid), INDEX IDX_2582F999F132696E (userid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_shows_refs (refid INT AUTO_INCREMENT NOT NULL, showid INT NOT NULL, ref VARCHAR(255) NOT NULL, INDEX IDX_86C0B071592D0E6F (showid), PRIMARY KEY(refid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_reviews (id INT AUTO_INCREMENT NOT NULL, showid INT NOT NULL, uid INT DEFAULT NULL, review LONGTEXT NOT NULL, `from` VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_4F8219D8592D0E6F (showid), INDEX IDX_4F8219D8539B0606 (uid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_stores (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, shortname VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_authtokens (id INT AUTO_INCREMENT NOT NULL, userid INT NOT NULL, token VARCHAR(50) NOT NULL, siteid INT NOT NULL, issued DATETIME NOT NULL, INDEX IDX_11BF9FFBF132696E (userid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_email_sigs (id INT AUTO_INCREMENT NOT NULL, uid INT DEFAULT NULL, sig LONGTEXT NOT NULL, INDEX IDX_9EB1EE3E539B0606 (uid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_forums (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, orderid INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_techies_positions (id INT AUTO_INCREMENT NOT NULL, position VARCHAR(255) NOT NULL, orderid INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_pages (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, parentid INT NOT NULL, sortcode INT NOT NULL, fulltitle VARCHAR(255) NOT NULL, secure TINYINT(1) NOT NULL, micro TINYINT(1) NOT NULL, help LONGTEXT NOT NULL, ghost TINYINT(1) NOT NULL, mode VARCHAR(50) DEFAULT NULL, allowsubpage TINYINT(1) NOT NULL, intertitle LONGTEXT NOT NULL, knowledgebase TINYINT(1) NOT NULL, getvars LONGTEXT NOT NULL, postvars LONGTEXT NOT NULL, usepage VARCHAR(255) NOT NULL, kbid INT NOT NULL, rssfeeds LONGTEXT NOT NULL, locked TINYINT(1) NOT NULL, virtual TINYINT(1) NOT NULL, param_parser TINYINT(1) NOT NULL, access_php LONGTEXT NOT NULL, subpagetemplate LONGTEXT NOT NULL, searchable TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_config (name VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_keywords (id INT AUTO_INCREMENT NOT NULL, pageid INT NOT NULL, kw VARCHAR(255) NOT NULL, INDEX IDX_B2CE00DB8BF4141 (pageid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_includes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_catalogue (id INT AUTO_INCREMENT NOT NULL, storeid INT NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_termdates (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, friendlyname VARCHAR(100) NOT NULL, startdate DATE NOT NULL, enddate DATE NOT NULL, firstweek INT NOT NULL, lastweek INT NOT NULL, displayweek TINYINT(1) NOT NULL, vacation VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_externalsites (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_search_cache (id INT AUTO_INCREMENT NOT NULL, keyword VARCHAR(200) NOT NULL, text LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, sindex INT NOT NULL, obsolete TINYINT(1) NOT NULL, linkcode VARCHAR(255) DEFAULT NULL, INDEX keyword (keyword), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_mailinglists_members (listid INT NOT NULL, uid INT NOT NULL, PRIMARY KEY(listid, uid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_email (emailid INT AUTO_INCREMENT NOT NULL, userid INT DEFAULT NULL, title VARCHAR(255) NOT NULL, public_add TINYINT(1) NOT NULL, summary LONGTEXT NOT NULL, `from` INT NOT NULL, listid LONGTEXT NOT NULL, deleteonsend TINYINT(1) NOT NULL, INDEX IDX_91B3DECEF132696E (userid), PRIMARY KEY(emailid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_people_data ADD CONSTRAINT FK_567E1F8F3DA5256D FOREIGN KEY (image_id) REFERENCES images (id)");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE3DA5256D FOREIGN KEY (image_id) REFERENCES images (id)");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FEE176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE5FB42679 FOREIGN KEY (authorizeid) REFERENCES acts_users (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE53F0F7FF FOREIGN KEY (primaryref) REFERENCES acts_shows_refs (refid) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_name_aliases ADD CONSTRAINT FK_355DA778217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D457167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE acts_auditions ADD CONSTRAINT FK_BFECDAF7592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_societies ADD CONSTRAINT FK_D8C37643DA5256D FOREIGN KEY (image_id) REFERENCES images (id)");
        $this->addSql("ALTER TABLE acts_techies ADD CONSTRAINT FK_4D00DAC2592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_users ADD CONSTRAINT FK_62A20753217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)");
        $this->addSql("ALTER TABLE acts_support ADD CONSTRAINT FK_A6F6197275DAD987 FOREIGN KEY (ownerid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_support ADD CONSTRAINT FK_A6F619725B919408 FOREIGN KEY (supportid) REFERENCES acts_support (id)");
        $this->addSql("ALTER TABLE acts_events ADD CONSTRAINT FK_78452C7AAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE acts_week_names ADD CONSTRAINT FK_C467B35F7EFD7106 FOREIGN KEY (time_period_id) REFERENCES acts_time_periods (id)");
        $this->addSql("ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E57167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E5550C4ED FOREIGN KEY (pid) REFERENCES acts_people_data (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_news_mentions ADD CONSTRAINT FK_9A671BDAB5A459A0 FOREIGN KEY (news_id) REFERENCES acts_news (id)");
        $this->addSql("ALTER TABLE acts_news_mentions ADD CONSTRAINT FK_9A671BDA81257D5D FOREIGN KEY (entity_id) REFERENCES acts_societies (id)");
        $this->addSql("ALTER TABLE acts_news ADD CONSTRAINT FK_E030B31081257D5D FOREIGN KEY (entity_id) REFERENCES acts_societies (id)");
        $this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0F592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_news_links ADD CONSTRAINT FK_56C215DAB5A459A0 FOREIGN KEY (news_id) REFERENCES acts_news (id)");
        $this->addSql("ALTER TABLE acts_external_users ADD CONSTRAINT FK_A75DA06CA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_external_users ADD CONSTRAINT FK_A75DA06C217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)");
        $this->addSql("ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB50646EEF703F FOREIGN KEY (issuerid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064C81B28E0 FOREIGN KEY (revokeid) REFERENCES acts_users (id) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE acts_api_auth_codes ADD CONSTRAINT FK_6FB69A2F19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)");
        $this->addSql("ALTER TABLE acts_api_auth_codes ADD CONSTRAINT FK_6FB69A2FA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_api_apps ADD CONSTRAINT FK_297ABD2CA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_api_apps ADD CONSTRAINT FK_297ABD2C9E6B1585 FOREIGN KEY (organisation_id) REFERENCES acts_societies (id)");
        $this->addSql("ALTER TABLE acts_api_authorisations ADD CONSTRAINT FK_EF3B9B55C1F2BE32 FOREIGN KEY (externalapp_id) REFERENCES acts_api_apps (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_api_authorisations ADD CONSTRAINT FK_EF3B9B55A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_api_access_tokens ADD CONSTRAINT FK_E452518D19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)");
        $this->addSql("ALTER TABLE acts_api_access_tokens ADD CONSTRAINT FK_E452518DA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_api_refresh_tokens ADD CONSTRAINT FK_CACE640E19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)");
        $this->addSql("ALTER TABLE acts_api_refresh_tokens ADD CONSTRAINT FK_CACE640EA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_email_aliases ADD CONSTRAINT FK_CFB21822539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_knowledgebase ADD CONSTRAINT FK_2582F9998BF4141 FOREIGN KEY (pageid) REFERENCES acts_pages (id)");
        $this->addSql("ALTER TABLE acts_knowledgebase ADD CONSTRAINT FK_2582F999F132696E FOREIGN KEY (userid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_shows_refs ADD CONSTRAINT FK_86C0B071592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_reviews ADD CONSTRAINT FK_4F8219D8592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_reviews ADD CONSTRAINT FK_4F8219D8539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_authtokens ADD CONSTRAINT FK_11BF9FFBF132696E FOREIGN KEY (userid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_email_sigs ADD CONSTRAINT FK_9EB1EE3E539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_keywords ADD CONSTRAINT FK_B2CE00DB8BF4141 FOREIGN KEY (pageid) REFERENCES acts_pages (id)");
        $this->addSql("ALTER TABLE acts_email ADD CONSTRAINT FK_91B3DECEF132696E FOREIGN KEY (userid) REFERENCES acts_users (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE acts_name_aliases DROP FOREIGN KEY FK_355DA778217BBB47");
        $this->addSql("ALTER TABLE acts_users DROP FOREIGN KEY FK_62A20753217BBB47");
        $this->addSql("ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E5550C4ED");
        $this->addSql("ALTER TABLE acts_external_users DROP FOREIGN KEY FK_A75DA06C217BBB47");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D457167AB4");
        $this->addSql("ALTER TABLE acts_auditions DROP FOREIGN KEY FK_BFECDAF7592D0E6F");
        $this->addSql("ALTER TABLE acts_techies DROP FOREIGN KEY FK_4D00DAC2592D0E6F");
        $this->addSql("ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E57167AB4");
        $this->addSql("ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0F592D0E6F");
        $this->addSql("ALTER TABLE acts_shows_refs DROP FOREIGN KEY FK_86C0B071592D0E6F");
        $this->addSql("ALTER TABLE acts_reviews DROP FOREIGN KEY FK_4F8219D8592D0E6F");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEAF648A81");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FEE176C6");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6");
        $this->addSql("ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7AAF648A81");
        $this->addSql("ALTER TABLE acts_news_mentions DROP FOREIGN KEY FK_9A671BDA81257D5D");
        $this->addSql("ALTER TABLE acts_news DROP FOREIGN KEY FK_E030B31081257D5D");
        $this->addSql("ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0FAF648A81");
        $this->addSql("ALTER TABLE acts_api_apps DROP FOREIGN KEY FK_297ABD2C9E6B1585");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE5FB42679");
        $this->addSql("ALTER TABLE acts_support DROP FOREIGN KEY FK_A6F6197275DAD987");
        $this->addSql("ALTER TABLE acts_external_users DROP FOREIGN KEY FK_A75DA06CA76ED395");
        $this->addSql("ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064539B0606");
        $this->addSql("ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB50646EEF703F");
        $this->addSql("ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064C81B28E0");
        $this->addSql("ALTER TABLE acts_api_auth_codes DROP FOREIGN KEY FK_6FB69A2FA76ED395");
        $this->addSql("ALTER TABLE acts_api_apps DROP FOREIGN KEY FK_297ABD2CA76ED395");
        $this->addSql("ALTER TABLE acts_api_authorisations DROP FOREIGN KEY FK_EF3B9B55A76ED395");
        $this->addSql("ALTER TABLE acts_api_access_tokens DROP FOREIGN KEY FK_E452518DA76ED395");
        $this->addSql("ALTER TABLE acts_api_refresh_tokens DROP FOREIGN KEY FK_CACE640EA76ED395");
        $this->addSql("ALTER TABLE acts_email_aliases DROP FOREIGN KEY FK_CFB21822539B0606");
        $this->addSql("ALTER TABLE acts_knowledgebase DROP FOREIGN KEY FK_2582F999F132696E");
        $this->addSql("ALTER TABLE acts_reviews DROP FOREIGN KEY FK_4F8219D8539B0606");
        $this->addSql("ALTER TABLE acts_authtokens DROP FOREIGN KEY FK_11BF9FFBF132696E");
        $this->addSql("ALTER TABLE acts_email_sigs DROP FOREIGN KEY FK_9EB1EE3E539B0606");
        $this->addSql("ALTER TABLE acts_email DROP FOREIGN KEY FK_91B3DECEF132696E");
        $this->addSql("ALTER TABLE acts_support DROP FOREIGN KEY FK_A6F619725B919408");
        $this->addSql("ALTER TABLE acts_week_names DROP FOREIGN KEY FK_C467B35F7EFD7106");
        $this->addSql("ALTER TABLE acts_news_mentions DROP FOREIGN KEY FK_9A671BDAB5A459A0");
        $this->addSql("ALTER TABLE acts_news_links DROP FOREIGN KEY FK_56C215DAB5A459A0");
        $this->addSql("ALTER TABLE acts_people_data DROP FOREIGN KEY FK_567E1F8F3DA5256D");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE3DA5256D");
        $this->addSql("ALTER TABLE acts_societies DROP FOREIGN KEY FK_D8C37643DA5256D");
        $this->addSql("ALTER TABLE acts_api_auth_codes DROP FOREIGN KEY FK_6FB69A2F19EB6921");
        $this->addSql("ALTER TABLE acts_api_authorisations DROP FOREIGN KEY FK_EF3B9B55C1F2BE32");
        $this->addSql("ALTER TABLE acts_api_access_tokens DROP FOREIGN KEY FK_E452518D19EB6921");
        $this->addSql("ALTER TABLE acts_api_refresh_tokens DROP FOREIGN KEY FK_CACE640E19EB6921");
        $this->addSql("ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE53F0F7FF");
        $this->addSql("ALTER TABLE acts_knowledgebase DROP FOREIGN KEY FK_2582F9998BF4141");
        $this->addSql("ALTER TABLE acts_keywords DROP FOREIGN KEY FK_B2CE00DB8BF4141");
        $this->addSql("DROP TABLE acts_people_data");
        $this->addSql("DROP TABLE acts_shows");
        $this->addSql("DROP TABLE acts_name_aliases");
        $this->addSql("DROP TABLE acts_performances");
        $this->addSql("DROP TABLE acts_auditions");
        $this->addSql("DROP TABLE acts_societies");
        $this->addSql("DROP TABLE acts_techies");
        $this->addSql("DROP TABLE acts_users");
        $this->addSql("DROP TABLE acts_support");
        $this->addSql("DROP TABLE acts_events");
        $this->addSql("DROP TABLE acts_week_names");
        $this->addSql("DROP TABLE acts_time_periods");
        $this->addSql("DROP TABLE acts_shows_people_link");
        $this->addSql("DROP TABLE acts_news_mentions");
        $this->addSql("DROP TABLE acts_news");
        $this->addSql("DROP TABLE acts_applications");
        $this->addSql("DROP TABLE acts_pendingaccess");
        $this->addSql("DROP TABLE acts_news_links");
        $this->addSql("DROP TABLE acts_external_users");
        $this->addSql("DROP TABLE acts_similar_names");
        $this->addSql("DROP TABLE acts_access");
        $this->addSql("DROP TABLE images");
        $this->addSql("DROP TABLE acts_api_auth_codes");
        $this->addSql("DROP TABLE acts_api_apps");
        $this->addSql("DROP TABLE acts_api_authorisations");
        $this->addSql("DROP TABLE acts_api_access_tokens");
        $this->addSql("DROP TABLE acts_api_refresh_tokens");
        $this->addSql("DROP TABLE acts_mailinglists");
        $this->addSql("DROP TABLE acts_email_aliases");
        $this->addSql("DROP TABLE acts_forums_messages");
        $this->addSql("DROP TABLE acts_email_items");
        $this->addSql("DROP TABLE acts_knowledgebase");
        $this->addSql("DROP TABLE acts_shows_refs");
        $this->addSql("DROP TABLE acts_reviews");
        $this->addSql("DROP TABLE acts_stores");
        $this->addSql("DROP TABLE acts_authtokens");
        $this->addSql("DROP TABLE acts_email_sigs");
        $this->addSql("DROP TABLE acts_forums");
        $this->addSql("DROP TABLE acts_techies_positions");
        $this->addSql("DROP TABLE acts_pages");
        $this->addSql("DROP TABLE acts_config");
        $this->addSql("DROP TABLE acts_keywords");
        $this->addSql("DROP TABLE acts_includes");
        $this->addSql("DROP TABLE acts_catalogue");
        $this->addSql("DROP TABLE acts_termdates");
        $this->addSql("DROP TABLE acts_externalsites");
        $this->addSql("DROP TABLE acts_search_cache");
        $this->addSql("DROP TABLE acts_mailinglists_members");
        $this->addSql("DROP TABLE acts_email");
    }
}
