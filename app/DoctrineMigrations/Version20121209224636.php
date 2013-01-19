<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121209224636 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql('ALTER DATABASE  `'.$this->connection->getDatabase().'`  CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_access` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_applications` MODIFY `text` BLOB, MODIFY `furtherinfo` BLOB');
        $this->addSql('ALTER TABLE `acts_applications` MODIFY `text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, MODIFY `furtherinfo` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE `acts_applications` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_auditions` MODIFY `location` BLOB');
        $this->addSql('ALTER TABLE `acts_auditions` MODIFY `location` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE `acts_auditions` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_authtokens` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_catalogue` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_config` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_email` MODIFY `title` BLOB, MODIFY `summary` BLOB');
        $this->addSql('ALTER TABLE `acts_email` MODIFY `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, MODIFY `summary` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE `acts_email` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_email_aliases` MODIFY `name` BLOB, MODIFY `email` BLOB');
        $this->addSql('ALTER TABLE `acts_email_aliases` MODIFY `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, MODIFY `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE `acts_email_aliases` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_email_items` MODIFY `title` BLOB, MODIFY `text` BLOB');
        $this->addSql('ALTER TABLE `acts_email_items` MODIFY `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, MODIFY `text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE `acts_email_items` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_email_sigs` MODIFY `sig` BLOB');
        $this->addSql('ALTER TABLE `acts_email_sigs` MODIFY `sig` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE `acts_email_sigs` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_events` MODIFY `text` BLOB, MODIFY `description` BLOB');
        $this->addSql('ALTER TABLE `acts_events` MODIFY `text` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, MODIFY `description` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE `acts_events` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_externalsites` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_forums` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->addSql("ALTER TABLE `acts_forums` CHANGE `name` name VARCHAR(255) NOT NULL");

        $this->addSql('ALTER TABLE `acts_forums_messages` MODIFY `subject` BLOB, MODIFY `message` BLOB');
        $this->addSql('ALTER TABLE `acts_forums_messages` MODIFY `subject` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, MODIFY `message` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE `acts_forums_messages` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_includes` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->addSql("ALTER TABLE `acts_includes` CHANGE `name` name VARCHAR(255) NOT NULL");

        $this->addSql("ALTER TABLE `acts_keywords` CHANGE `kw` kw VARCHAR(255) NOT NULL");
        $this->addSql('ALTER TABLE `acts_keywords` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_knowledgebase` MODIFY `text` BLOB');
        $this->addSql('ALTER TABLE `acts_knowledgebase` MODIFY `text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE `acts_knowledgebase` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_mailinglists` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->addSql("ALTER TABLE `acts_mailinglists` CHANGE `name` name VARCHAR(255) NOT NULL, CHANGE `shortname` shortname VARCHAR(100) NOT NULL");

        $this->addSql('ALTER TABLE `acts_mailinglists_members` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_pages` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->addSql("ALTER TABLE `acts_pages` CHANGE `title` title VARCHAR(255) NOT NULL,
            CHANGE `fulltitle` fulltitle VARCHAR(255) NOT NULL,
            CHANGE `mode` mode VARCHAR(50) DEFAULT NULL, CHANGE `allowsubpage` allowsubpage TINYINT(1) NOT NULL, CHANGE `usepage` usepage VARCHAR(255) NOT NULL");

        $this->addSql('ALTER TABLE  `acts_pendingaccess` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->addSql("ALTER TABLE `acts_pendingaccess` CHANGE `email` email VARCHAR(255) NOT NULL");

        $this->addSql('ALTER TABLE `acts_people_data` MODIFY `name` BLOB');
        $this->addSql('ALTER TABLE `acts_people_data` MODIFY `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE  `acts_people_data` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_performances` MODIFY `venue` BLOB');
        $this->addSql('ALTER TABLE `acts_performances` MODIFY `venue` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE `acts_performances` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_reviews` MODIFY `from` BLOB, MODIFY `review` BLOB');
        $this->addSql('ALTER TABLE `acts_reviews` MODIFY `from` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, MODIFY `review` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE  `acts_reviews` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_shows_people_link` MODIFY `role` BLOB');
        $this->addSql('ALTER TABLE `acts_shows_people_link` MODIFY `role` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, MODIFY `type` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE  `acts_shows_people_link` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_search_cache` MODIFY `text` BLOB, MODIFY `url` BLOB, MODIFY `linkcode` BLOB');
        $this->addSql('ALTER TABLE `acts_search_cache` MODIFY `text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `url` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `linkcode` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL');
        $this->addSql('ALTER TABLE  `acts_search_cache` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql("DROP INDEX `title` ON `acts_shows`"); //InnoDB does not support fulltext indexes
        $this->addSql('ALTER TABLE `acts_shows` MODIFY `title` BLOB, MODIFY `dates` BLOB, MODIFY `author` BLOB, MODIFY `prices` BLOB, MODIFY `description` BLOB, MODIFY `venue` BLOB,
            MODIFY `society` BLOB, MODIFY `audextra` BLOB, MODIFY `photourl` BLOB, MODIFY `bookingcode` BLOB');
        $this->addSql('ALTER TABLE `acts_shows` MODIFY `title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `author` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `dates` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `prices` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `description` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
            MODIFY `venue` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `society` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
            MODIFY `audextra` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
            MODIFY `photourl` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
            MODIFY `bookingcode` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE  `acts_shows` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql("ALTER TABLE `acts_shows_refs` CHANGE `ref` ref VARCHAR(255) NOT NULL");
        $this->addSql('ALTER TABLE  `acts_shows_refs` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_societies` MODIFY `name` BLOB, MODIFY `description` BLOB, MODIFY `shortname` BLOB, MODIFY `college` BLOB, MODIFY `logourl` BLOB');
        $this->addSql('ALTER TABLE `acts_societies` MODIFY `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `shortname` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `description` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
            MODIFY `college` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
            MODIFY `logourl` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
            MODIFY `id` INT NOT NULL');
        $this->addSql('ALTER TABLE  `acts_societies` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql("ALTER TABLE `acts_stores` CHANGE `name` name VARCHAR(255) NOT NULL, CHANGE `shortname` shortname VARCHAR(100) NOT NULL");
        $this->addSql('ALTER TABLE  `acts_stores` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_support` MODIFY `from` BLOB, MODIFY `to` BLOB, MODIFY `cc` BLOB, MODIFY `subject` BLOB, MODIFY `body` BLOB');
        $this->addSql('ALTER TABLE `acts_support` MODIFY `from` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `to` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `cc` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `subject` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `body` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `state` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE  `acts_support` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE `acts_techies` MODIFY `positions` BLOB, MODIFY `contact` BLOB, MODIFY `deadlinetime` BLOB, MODIFY `techextra` BLOB');
        $this->addSql('ALTER TABLE `acts_techies`
            MODIFY `positions` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `contact` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `deadlinetime` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            MODIFY `techextra` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql('ALTER TABLE  `acts_techies` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE  `acts_techies_positions` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->addSql("ALTER TABLE `acts_techies_positions` CHANGE `position` position VARCHAR(255) NOT NULL, MODIFY `orderid` INT NOT NULL");

        $this->addSql('ALTER TABLE  `acts_termdates` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->addSql("ALTER TABLE `acts_termdates` CHANGE `name` name VARCHAR(100) NOT NULL, CHANGE `friendlyname` friendlyname VARCHAR(100) NOT NULL, CHANGE `vacation` vacation VARCHAR(100) NOT NULL");

        $this->addSql('ALTER TABLE `acts_users` MODIFY `name` BLOB, MODIFY `email` BLOB');
        $this->addSql('ALTER TABLE `acts_users` MODIFY `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,  MODIFY `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL');
        $this->addSql("ALTER TABLE `acts_users` CHANGE `pass` pass VARCHAR(32) NOT NULL, CHANGE `occupation` occupation VARCHAR(255) DEFAULT NULL, CHANGE `graduation` graduation VARCHAR(255) DEFAULT NULL, CHANGE `tel` tel VARCHAR(50) DEFAULT NULL, CHANGE `resetcode` resetcode VARCHAR(32) NOT NULL");
        $this->addSql('ALTER TABLE `acts_users` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE  `footprints` ENGINE = INNODB, CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');


        $this->addSql("ALTER TABLE `acts_techies_positions` ADD id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)");
        $this->addSql("DROP INDEX `id` ON `acts_access`");
        $this->addSql("ALTER TABLE `acts_access` CHANGE `rid` rid INT NOT NULL, CHANGE `uid` uid INT NOT NULL, CHANGE `type` type VARCHAR(255) NOT NULL, CHANGE `issuerid` issuerid INT DEFAULT NULL, CHANGE `creationdate` creationdate DATE NOT NULL, CHANGE `revokeid` revokeid INT DEFAULT NULL, CHANGE `revokedate` revokedate DATE NOT NULL, CHANGE `contact` contact TINYINT(1) NOT NULL");
        $this->addSql("CREATE INDEX IDX_2DAB5064539B0606 ON acts_access (uid)");
        $this->addSql("CREATE INDEX IDX_2DAB50646EEF703F ON acts_access (issuerid)");
        $this->addSql("CREATE INDEX IDX_2DAB5064C81B28E0 ON acts_access (revokeid)");
        $this->addSql("DELETE acts_access FROM acts_access LEFT JOIN acts_users ON  acts_access.uid = acts_users.id WHERE acts_users.id IS NULL");
        $this->addSql("UPDATE acts_access AS a LEFT JOIN acts_users AS u ON  a.issuerid = u.id SET a.issuerid = NULL WHERE u.id IS NULL");
        $this->addSql("UPDATE acts_access AS a LEFT JOIN acts_users AS u ON  a.revokeid = u.id SET a.revokeid = NULL WHERE u.id IS NULL");
        $this->addSql("ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB50646EEF703F FOREIGN KEY (issuerid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB5064C81B28E0 FOREIGN KEY (revokeid) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE `acts_email` CHANGE `userid` userid INT NULL, CHANGE `public_add` public_add TINYINT(1) NOT NULL, CHANGE `from` `from` INT NOT NULL, CHANGE `deleteonsend` deleteonsend TINYINT(1) NOT NULL");
        $this->addSql("UPDATE acts_email AS e LEFT JOIN acts_users AS u ON  e.userid = u.id SET e.userid = NULL WHERE u.id IS NULL");
        $this->addSql("ALTER TABLE acts_email ADD CONSTRAINT FK_91B3DECEF132696E FOREIGN KEY (userid) REFERENCES acts_users (id)");
        $this->addSql("CREATE INDEX IDX_91B3DECEF132696E ON acts_email (userid)");
        $this->addSql("ALTER TABLE `acts_societies` CHANGE `type` type TINYINT(1) NOT NULL, CHANGE `affiliate` affiliate TINYINT(1) NOT NULL");
        $this->addSql("ALTER TABLE `acts_email_sigs` CHANGE `uid` uid INT NOT NULL");
        $this->addSql("ALTER TABLE acts_email_sigs ADD CONSTRAINT FK_9EB1EE3E539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)");
        $this->addSql("CREATE INDEX IDX_9EB1EE3E539B0606 ON acts_email_sigs (uid)");
        $this->addSql("ALTER TABLE `acts_performances` CHANGE `sid` sid INT NOT NULL, CHANGE `startdate` startdate DATE NOT NULL, CHANGE `enddate` enddate DATE NOT NULL, CHANGE `excludedate` excludedate DATE NOT NULL, CHANGE `time` time TIME NOT NULL, CHANGE `venid` venid INT NULL");
        $this->addSql("DELETE acts_performances FROM acts_performances LEFT JOIN acts_shows ON  acts_performances.sid = acts_shows.id WHERE acts_shows.id IS NULL");
        $this->addSql("UPDATE acts_performances AS p LEFT JOIN acts_societies AS s ON  p.venid = s.id SET p.venid = NULL WHERE s.id IS NULL");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D457167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_performances ADD CONSTRAINT FK_E317F2D4E176C6 FOREIGN KEY (venid) REFERENCES acts_societies (id)");
        $this->addSql("CREATE INDEX IDX_E317F2D457167AB4 ON acts_performances (sid)");
        $this->addSql("CREATE INDEX IDX_E317F2D4E176C6 ON acts_performances (venid)");
        $this->addSql("ALTER TABLE `acts_keywords` CHANGE `pageid` pageid INT NOT NULL");
        $this->addSql("ALTER TABLE acts_keywords ADD CONSTRAINT FK_B2CE00DB8BF4141 FOREIGN KEY (pageid) REFERENCES acts_pages (id)");
        $this->addSql("CREATE INDEX IDX_B2CE00DB8BF4141 ON acts_keywords (pageid)");
        $this->addSql("ALTER TABLE `acts_shows_refs` CHANGE `showid` showid INT NOT NULL");
        $this->addSql("DELETE acts_shows_refs FROM acts_shows_refs LEFT JOIN acts_shows ON  acts_shows_refs.showid = acts_shows.id WHERE acts_shows.id IS NULL");
        $this->addSql("ALTER TABLE acts_shows_refs ADD CONSTRAINT FK_86C0B071592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
        $this->addSql("CREATE INDEX IDX_86C0B071592D0E6F ON acts_shows_refs (showid)");
        $this->addSql("ALTER TABLE `acts_mailinglists` CHANGE `public` public TINYINT(1) NOT NULL, CHANGE `defaultsubscribe` defaultsubscribe TINYINT(1) NOT NULL");
        $this->addSql("ALTER TABLE `acts_pages` CHANGE `parentid` parentid INT NOT NULL, CHANGE `sortcode` sortcode INT NOT NULL, CHANGE `secure` secure TINYINT(1) NOT NULL, CHANGE `micro` micro TINYINT(1) NOT NULL, CHANGE `ghost` ghost TINYINT(1) NOT NULL, CHANGE `mode` mode VARCHAR(50) DEFAULT NULL, CHANGE `allowsubpage` allowsubpage TINYINT(1) NOT NULL, CHANGE `knowledgebase` knowledgebase TINYINT(1) NOT NULL, CHANGE `kbid` kbid INT NOT NULL, CHANGE `locked` locked TINYINT(1) NOT NULL, CHANGE `virtual` virtual TINYINT(1) NOT NULL, CHANGE `param_parser` param_parser TINYINT(1) NOT NULL");
        $this->addSql("ALTER TABLE `acts_config` CHANGE `name` name VARCHAR(255) NOT NULL");
        $this->addSql("ALTER TABLE `acts_forums` CHANGE `orderid` orderid INT NOT NULL");
        $this->addSql("ALTER TABLE `acts_pendingaccess` CHANGE `rid` rid INT NOT NULL, CHANGE `type` type VARCHAR(255) NOT NULL, CHANGE `issuerid` issuerid INT NOT NULL, CHANGE `creationdate` creationdate DATE NOT NULL");
        $this->addSql("ALTER TABLE `acts_mailinglists_members` CHANGE `listid` listid INT NOT NULL, CHANGE `uid` uid INT NOT NULL");
        $this->addSql("ALTER TABLE `acts_shows_people_link` CHANGE `sid` sid INT NULL, CHANGE `type` type VARCHAR(20) NOT NULL, CHANGE `order` `order` INT NOT NULL, CHANGE `pid` pid INT NULL");
        $this->addSql("UPDATE acts_shows_people_link AS l LEFT JOIN acts_shows AS s ON  l.sid = s.id SET l.sid = NULL WHERE s.id IS NULL");
        $this->addSql("UPDATE acts_shows_people_link AS l LEFT JOIN acts_people_data AS p ON  l.pid = p.id SET l.pid = NULL WHERE p.id IS NULL");
        $this->addSql("ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E57167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_shows_people_link ADD CONSTRAINT FK_2F5AB85E5550C4ED FOREIGN KEY (pid) REFERENCES acts_people_data (id)");
        $this->addSql("DROP INDEX `token` ON `acts_authtokens`");
        $this->addSql("ALTER TABLE acts_authtokens ADD CONSTRAINT FK_11BF9FFBF132696E FOREIGN KEY (userid) REFERENCES acts_users (id)");
        $this->addSql("CREATE INDEX IDX_11BF9FFBF132696E ON acts_authtokens (userid)");
        $this->addSql("CREATE INDEX token ON acts_authtokens (token)");
        $this->addSql("ALTER TABLE `acts_termdates` DROP INDEX `id`, ADD PRIMARY KEY (`id`)");
        $this->addSql("ALTER TABLE `acts_termdates` CHANGE `startdate` startdate DATE NOT NULL, CHANGE `enddate` enddate DATE NOT NULL, CHANGE `firstweek` firstweek TINYINT(1) NOT NULL, CHANGE `lastweek` lastweek TINYINT(1) NOT NULL, CHANGE `displayweek` displayweek TINYINT(1) NOT NULL");
        $this->addSql("ALTER TABLE `acts_search_cache` CHANGE `obsolete` obsolete TINYINT(1) NOT NULL");
        $this->addSql("ALTER TABLE `acts_events` DROP INDEX `id`, ADD PRIMARY KEY (`id`)");
        $this->addSql("ALTER TABLE `acts_events` CHANGE `endtime` endtime TIME NOT NULL, CHANGE `starttime` starttime TIME NOT NULL, CHANGE `date` date DATE NOT NULL, CHANGE `linkid` linkid INT NOT NULL, CHANGE `socid` socid INT NOT NULL");
        $this->addSql("CREATE INDEX IDX_78452C7AAF648A81 ON acts_events (socid)");
        $this->addSql("ALTER TABLE `acts_email_items` CHANGE `emailid` emailid INT NOT NULL, CHANGE `orderid` orderid DOUBLE PRECISION NOT NULL, CHANGE `creatorid` creatorid INT NOT NULL, CHANGE `created` created DATETIME NOT NULL, CHANGE `protect` protect TINYINT(1) NOT NULL");
        $this->addSql("ALTER TABLE `acts_email_aliases` CHANGE `uid` uid INT NOT NULL");
        $this->addSql("ALTER TABLE acts_email_aliases ADD CONSTRAINT FK_CFB21822539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)");
        $this->addSql("CREATE INDEX IDX_CFB21822539B0606 ON acts_email_aliases (uid)");
        $this->addSql("ALTER TABLE `acts_catalogue` CHANGE `storeid` storeid INT NOT NULL");
        $this->addSql("ALTER TABLE `acts_reviews` CHANGE `showid` showid INT NOT NULL, CHANGE `uid` uid INT NOT NULL, CHANGE `created` created DATETIME NOT NULL");
        $this->addSql("ALTER TABLE acts_reviews ADD CONSTRAINT FK_4F8219D8592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE acts_reviews ADD CONSTRAINT FK_4F8219D8539B0606 FOREIGN KEY (uid) REFERENCES acts_users (id)");
        $this->addSql("CREATE INDEX IDX_4F8219D8592D0E6F ON acts_reviews (showid)");
        $this->addSql("CREATE INDEX IDX_4F8219D8539B0606 ON acts_reviews (uid)");
        $this->addSql("ALTER TABLE `acts_users` CHANGE `registered` registered DATE NOT NULL, CHANGE `login` login DATE NOT NULL, CHANGE `contact` contact TINYINT(1) NOT NULL, CHANGE `alumni` alumni TINYINT(1) NOT NULL, CHANGE `publishemail` publishemail TINYINT(1) NOT NULL, CHANGE `dbemail` dbemail TINYINT(1) DEFAULT NULL, CHANGE `dbphone` dbphone TINYINT(1) DEFAULT NULL, CHANGE `forumnotify` forumnotify TINYINT(1) DEFAULT NULL, CHANGE `threadmessages` threadmessages TINYINT(1) DEFAULT NULL, CHANGE `reversetime` reversetime TINYINT(1) NOT NULL");
        $this->addSql("ALTER TABLE `acts_people_data` CHANGE `id` id INT NOT NULL, CHANGE `mapto` mapto INT NOT NULL, CHANGE `norobots` norobots TINYINT(1) NOT NULL");
        $this->addSql("ALTER TABLE `acts_techies` DROP INDEX `showid`, ADD INDEX `IDX_4D00DAC2592D0E6F` (`showid`)");
        $this->addSql("ALTER TABLE `acts_techies` CHANGE `showid` showid INT DEFAULT NULL, CHANGE `deadline` deadline TINYINT(1) NOT NULL, CHANGE `expiry` expiry DATE NOT NULL, CHANGE `display` display TINYINT(1) NOT NULL, CHANGE `remindersent` remindersent TINYINT(1) NOT NULL, CHANGE `lastupdated` lastupdated DATETIME NOT NULL");
        $this->addSql("UPDATE acts_techies AS t LEFT JOIN acts_shows AS s ON  t.showid = s.id SET t.showid = NULL WHERE s.id IS NULL");
        $this->addSql("ALTER TABLE acts_techies ADD CONSTRAINT FK_4D00DAC2592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
        $this->addSql("ALTER TABLE `footprints` DROP INDEX `from`, ADD PRIMARY KEY (`from`)");
        $this->addSql("ALTER TABLE `footprints` CHANGE `from` `from` INT AUTO_INCREMENT NOT NULL, CHANGE `to` `to` INT NOT NULL, CHANGE `time` time INT NOT NULL");
        $this->addSql("ALTER TABLE `acts_knowledgebase` CHANGE `pageid` pageid INT NULL, CHANGE `userid` userid INT NULL, CHANGE `date` date DATETIME NOT NULL");
        $this->addSql("UPDATE acts_knowledgebase AS k LEFT JOIN acts_pages AS p ON  k.pageid = p.id SET k.pageid = NULL WHERE p.id IS NULL");
        $this->addSql("UPDATE acts_knowledgebase AS k LEFT JOIN acts_users AS u ON  k.userid = u.id SET k.userid = NULL WHERE u.id IS NULL");
        $this->addSql("ALTER TABLE acts_knowledgebase ADD CONSTRAINT FK_2582F9998BF4141 FOREIGN KEY (pageid) REFERENCES acts_pages (id)");
        $this->addSql("ALTER TABLE acts_knowledgebase ADD CONSTRAINT FK_2582F999F132696E FOREIGN KEY (userid) REFERENCES acts_users (id)");
        $this->addSql("CREATE INDEX IDX_2582F9998BF4141 ON acts_knowledgebase (pageid)");
        $this->addSql("CREATE INDEX IDX_2582F999F132696E ON acts_knowledgebase (userid)");
        $this->addSql("ALTER TABLE `acts_forums_messages` CHANGE `replyid` replyid INT NOT NULL, CHANGE `forumid` forumid INT NOT NULL, CHANGE `uid` uid INT NOT NULL, CHANGE `datetime` datetime DATETIME NOT NULL, CHANGE `resourceid` resourceid INT NOT NULL, CHANGE `ancestorid` ancestorid INT NOT NULL, CHANGE `lastpost` lastpost DATETIME NOT NULL");
        $this->addSql("ALTER TABLE `acts_support` CHANGE `supportid` supportid INT NOT NULL, CHANGE `ownerid` ownerid INT NOT NULL, CHANGE `datetime` datetime DATETIME NOT NULL");
        $this->addSql("DROP INDEX `id` ON `acts_shows`");
        $this->addSql("ALTER TABLE `acts_shows` CHANGE `id` id INT NOT NULL, CHANGE `excludedate` excludedate DATE NOT NULL, CHANGE `techsend` techsend TINYINT(1) NOT NULL, CHANGE `actorsend` actorsend TINYINT(1) NOT NULL, CHANGE `socid` socid INT NOT NULL, CHANGE `venid` venid INT NOT NULL, CHANGE `authorizeid` authorizeid INT NOT NULL, CHANGE `entered` entered TINYINT(1) NOT NULL, CHANGE `entryexpiry` entryexpiry DATE NOT NULL, CHANGE `category` category VARCHAR(255) NOT NULL, CHANGE `primaryref` primaryref INT NOT NULL, CHANGE `timestamp` timestamp DATETIME NOT NULL");
        $this->addSql("ALTER TABLE `acts_auditions` CHANGE `date` date DATE NOT NULL, CHANGE `starttime` starttime TIME NOT NULL, CHANGE `endtime` endtime TIME NOT NULL, CHANGE `showid` showid INT NULL, CHANGE `display` display TINYINT(1) NOT NULL, CHANGE `nonscheduled` nonscheduled TINYINT(1) NOT NULL");
        $this->addSql("UPDATE acts_auditions AS a LEFT JOIN acts_shows AS s ON  a.showid = s.id SET a.showid = NULL WHERE s.id IS NULL");
        $this->addSql("ALTER TABLE acts_auditions ADD CONSTRAINT FK_BFECDAF7592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
        $this->addSql("CREATE INDEX IDX_BFECDAF7592D0E6F ON acts_auditions (showid)");
        $this->addSql("ALTER TABLE `acts_applications` CHANGE `showid` showid INT NULL, CHANGE `socid` socid INT NULL, CHANGE `deadlinedate` deadlinedate DATE NOT NULL, CHANGE `deadlinetime` deadlinetime TIME NOT NULL");
        $this->addSql("UPDATE acts_applications AS a LEFT JOIN acts_shows AS s ON  a.showid = s.id SET a.showid = NULL WHERE s.id IS NULL");
        $this->addSql("UPDATE acts_applications AS a LEFT JOIN acts_societies AS s ON  a.socid = s.id SET a.socid = NULL WHERE s.id IS NULL");
        $this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0F592D0E6F FOREIGN KEY (showid) REFERENCES acts_shows (id)");
        //$this->addSql("ALTER TABLE acts_applications ADD CONSTRAINT FK_95ED3F0FAF648A81 FOREIGN KEY (socid) REFERENCES acts_societies (id)");
        $this->addSql("CREATE INDEX IDX_95ED3F0F592D0E6F ON acts_applications (showid)");
        $this->addSql("CREATE INDEX IDX_95ED3F0FAF648A81 ON acts_applications (socid)");


    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("ALTER TABLE `acts_auditions` CHANGE `location` location LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_email` CHANGE `title` title LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_email_aliases` CHANGE `name` name LONGTEXT NOT NULL, CHANGE `email` email LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_email_items` CHANGE `text` LONGTEXT LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_events` CHANGE `text` LONGTEXT LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_forums` CHANGE `name` name LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_includes` CHANGE `name` name LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_keywords` CHANGE `kw` kw LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_mailinglists` CHANGE `name` name LONGTEXT NOT NULL, CHANGE `shortname` shortname LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_pages` CHANGE `title` title LONGTEXT NOT NULL, CHANGE `fulltitle` fulltitle LONGTEXT NOT NULL, CHANGE `mode` mode VARCHAR(255) DEFAULT NULL, CHANGE `allowsubpage` allowsubpage INT NOT NULL, CHANGE `usepage` usepage LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_pendingaccess` CHANGE `email` email LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_people_data` CHANGE `name` name LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_performances` CHANGE `venue` venue LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_reviews` CHANGE `from` `from` LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_search_cache` CHANGE `url` url LONGTEXT NOT NULL, CHANGE `linkcode` linkcode VARCHAR(2000) DEFAULT NULL");
        $this->addSql("ALTER TABLE `acts_shows` CHANGE `dates` dates LONGTEXT NOT NULL, CHANGE `title` title LONGTEXT NOT NULL, CHANGE `author` author LONGTEXT NOT NULL, CHANGE `prices` prices LONGTEXT NOT NULL, CHANGE `venue` venue LONGTEXT NOT NULL, CHANGE `society` society LONGTEXT DEFAULT NULL, CHANGE `bookingcode` bookingcode LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_shows_people_link` CHANGE `type` type VARCHAR(255) NOT NULL, CHANGE `role` role LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_shows_refs` CHANGE `ref` ref LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_societies` CHANGE `name` name LONGTEXT NOT NULL, CHANGE `shortname` shortname LONGTEXT NOT NULL, CHANGE `college` college LONGTEXT DEFAULT NULL, CHANGE `logourl` logourl LONGTEXT DEFAULT NULL");
        $this->addSql("ALTER TABLE `acts_stores` CHANGE `name` name LONGTEXT NOT NULL, CHANGE `shortname` shortname LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_support` CHANGE `from` `from` LONGTEXT NOT NULL, CHANGE `to` `to` LONGTEXT NOT NULL, CHANGE `cc` cc LONGTEXT NOT NULL, CHANGE `subject` subject LONGTEXT NOT NULL, CHANGE `state` state VARCHAR(255) NOT NULL");
        $this->addSql("ALTER TABLE `acts_techies` CHANGE `deadlinetime` deadlinetime LONGTEXT NOT NULL");
        $this->addSql("ALTER TABLE `acts_techies_positions` CHANGE `position` position LONGTEXT NOT NULL, CHANGE `orderid` orderid DOUBLE PRECISION NOT NULL");
        $this->addSql("ALTER TABLE `acts_termdates` CHANGE `name` name LONGTEXT NOT NULL, CHANGE `friendlyname` friendlyname LONGTEXT NOT NULL, CHANGE `vacation` vacation LONGTEXT NOT NULL");

        $this->addSql("ALTER TABLE `acts_users` CHANGE `name` name LONGTEXT NOT NULL, CHANGE `email` email LONGTEXT NOT NULL, CHANGE `pass` pass LONGTEXT NOT NULL, CHANGE `occupation` occupation LONGTEXT NOT NULL, CHANGE `graduation` graduation LONGTEXT NOT NULL, CHANGE `tel` tel LONGTEXT NOT NULL, CHANGE `resetcode` resetcode LONGTEXT NOT NULL");

        $this->addSql("ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064539B0606");
        $this->addSql("ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB50646EEF703F");
        $this->addSql("ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB5064C81B28E0");
        $this->addSql("DROP INDEX `IDX_2DAB5064539B0606` ON `acts_access`");
        $this->addSql("DROP INDEX `IDX_2DAB50646EEF703F` ON `acts_access`");
        $this->addSql("DROP INDEX `IDX_2DAB5064C81B28E0` ON `acts_access`");
        $this->addSql("ALTER TABLE `acts_access` CHANGE `uid` uid INT DEFAULT 0 NOT NULL, CHANGE `issuerid` issuerid INT DEFAULT 0 NOT NULL, CHANGE `revokeid` revokeid INT DEFAULT 0 NOT NULL, CHANGE `rid` rid INT DEFAULT 0 NOT NULL, CHANGE `type` type VARCHAR(255) DEFAULT 'show' NOT NULL, CHANGE `creationdate` creationdate DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `revokedate` revokedate DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `contact` contact TINYINT(1) DEFAULT '0' NOT NULL");
        $this->addSql("CREATE UNIQUE INDEX id ON acts_access (id)");
        $this->addSql("ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0F592D0E6F");
        //$this->addSql("ALTER TABLE acts_applications DROP FOREIGN KEY FK_95ED3F0FAF648A81");
        $this->addSql("DROP INDEX `IDX_95ED3F0F592D0E6F` ON `acts_applications`");
        $this->addSql("DROP INDEX `IDX_95ED3F0FAF648A81` ON `acts_applications`");
        $this->addSql("ALTER TABLE `acts_applications` CHANGE `showid` showid INT DEFAULT 0 NOT NULL, CHANGE `socid` socid INT DEFAULT 0 NOT NULL, CHANGE `deadlinedate` deadlinedate DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `deadlinetime` deadlinetime TIME DEFAULT '00:00:00' NOT NULL");
        $this->addSql("ALTER TABLE acts_auditions DROP FOREIGN KEY FK_BFECDAF7592D0E6F");
        $this->addSql("DROP INDEX `IDX_BFECDAF7592D0E6F` ON `acts_auditions`");
        $this->addSql("ALTER TABLE `acts_auditions` CHANGE `showid` showid INT DEFAULT 0 NOT NULL, CHANGE `date` date DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `starttime` starttime TIME DEFAULT '00:00:00' NOT NULL, CHANGE `endtime` endtime TIME DEFAULT '00:00:00' NOT NULL, CHANGE `display` display TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `nonscheduled` nonscheduled TINYINT(1) DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE acts_authtokens DROP FOREIGN KEY FK_11BF9FFBF132696E");
        $this->addSql("DROP INDEX `IDX_11BF9FFBF132696E` ON `acts_authtokens`");
        $this->addSql("DROP INDEX `token` ON `acts_authtokens`");
        $this->addSql("CREATE UNIQUE INDEX token ON acts_authtokens (token)");
        $this->addSql("ALTER TABLE `acts_catalogue` CHANGE `storeid` storeid INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE `acts_config` CHANGE `name` name VARCHAR(255) DEFAULT '' NOT NULL");
        $this->addSql("ALTER TABLE acts_email DROP FOREIGN KEY FK_91B3DECEF132696E");
        $this->addSql("DROP INDEX `IDX_91B3DECEF132696E` ON `acts_email`");
        $this->addSql("ALTER TABLE `acts_email` CHANGE `userid` userid INT DEFAULT 0 NOT NULL, CHANGE `public_add` public_add TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `from` `from` INT DEFAULT 0 NOT NULL, CHANGE `deleteonsend` deleteonsend TINYINT(1) DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE acts_email_aliases DROP FOREIGN KEY FK_CFB21822539B0606");
        $this->addSql("DROP INDEX `IDX_CFB21822539B0606` ON `acts_email_aliases`");
        $this->addSql("ALTER TABLE `acts_email_aliases` CHANGE `uid` uid INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE `acts_email_items` CHANGE `emailid` emailid INT DEFAULT 0 NOT NULL, CHANGE `orderid` orderid DOUBLE PRECISION DEFAULT '0' NOT NULL, CHANGE `creatorid` creatorid INT DEFAULT 0 NOT NULL, CHANGE `created` created DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL, CHANGE `protect` protect TINYINT(1) DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE acts_email_sigs DROP FOREIGN KEY FK_9EB1EE3E539B0606");
        $this->addSql("DROP INDEX `IDX_9EB1EE3E539B0606` ON `acts_email_sigs`");
        $this->addSql("ALTER TABLE `acts_email_sigs` CHANGE `uid` uid INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE `acts_events` DROP INDEX `primary`, ADD UNIQUE INDEX `id` (`id`)");
        //$this->addSql("ALTER TABLE acts_events DROP FOREIGN KEY FK_78452C7AAF648A81");
        $this->addSql("DROP INDEX `IDX_78452C7AAF648A81` ON `acts_events`");
        $this->addSql("ALTER TABLE `acts_events` CHANGE `socid` socid INT DEFAULT 0 NOT NULL, CHANGE `endtime` endtime TIME DEFAULT '00:00:00' NOT NULL, CHANGE `starttime` starttime TIME DEFAULT '00:00:00' NOT NULL, CHANGE `date` date DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `linkid` linkid INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE `acts_forums` CHANGE `orderid` orderid INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE `acts_forums_messages` CHANGE `replyid` replyid INT DEFAULT 0 NOT NULL, CHANGE `forumid` forumid INT DEFAULT 0 NOT NULL, CHANGE `uid` uid INT DEFAULT 0 NOT NULL, CHANGE `datetime` datetime DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL, CHANGE `resourceid` resourceid INT DEFAULT 0 NOT NULL, CHANGE `ancestorid` ancestorid INT DEFAULT 0 NOT NULL, CHANGE `lastpost` lastpost DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
        $this->addSql("ALTER TABLE acts_keywords DROP FOREIGN KEY FK_B2CE00DB8BF4141");
        $this->addSql("DROP INDEX `IDX_B2CE00DB8BF4141` ON `acts_keywords`");
        $this->addSql("ALTER TABLE `acts_keywords` CHANGE `pageid` pageid INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE acts_knowledgebase DROP FOREIGN KEY FK_2582F9998BF4141");
        $this->addSql("ALTER TABLE acts_knowledgebase DROP FOREIGN KEY FK_2582F999F132696E");
        $this->addSql("DROP INDEX `IDX_2582F9998BF4141` ON `acts_knowledgebase`");
        $this->addSql("DROP INDEX `IDX_2582F999F132696E` ON `acts_knowledgebase`");
        $this->addSql("ALTER TABLE `acts_knowledgebase` CHANGE `pageid` pageid INT DEFAULT 0 NOT NULL, CHANGE `userid` userid INT DEFAULT 0 NOT NULL, CHANGE `date` date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
        $this->addSql("ALTER TABLE `acts_mailinglists` CHANGE `public` public TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `defaultsubscribe` defaultsubscribe TINYINT(1) DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE `acts_mailinglists_members` CHANGE `listid` listid INT DEFAULT 0 NOT NULL, CHANGE `uid` uid INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE `acts_pages` CHANGE `parentid` parentid INT DEFAULT 0 NOT NULL, CHANGE `sortcode` sortcode INT DEFAULT 0 NOT NULL, CHANGE `secure` secure TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `micro` micro TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `ghost` ghost TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `mode` mode VARCHAR(255) DEFAULT 'normal', CHANGE `allowsubpage` allowsubpage INT DEFAULT 0 NOT NULL, CHANGE `knowledgebase` knowledgebase TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `kbid` kbid INT DEFAULT 0 NOT NULL, CHANGE `locked` locked TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `virtual` virtual TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `param_parser` param_parser TINYINT(1) DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE `acts_pendingaccess` CHANGE `rid` rid INT DEFAULT 0 NOT NULL, CHANGE `type` type VARCHAR(255) DEFAULT 'show' NOT NULL, CHANGE `issuerid` issuerid INT DEFAULT 0 NOT NULL, CHANGE `creationdate` creationdate DATE DEFAULT '0000-00-00' NOT NULL");
        $this->addSql("ALTER TABLE `acts_people_data` CHANGE `mapto` mapto INT DEFAULT 0 NOT NULL, CHANGE `norobots` norobots TINYINT(1) DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D457167AB4");
        $this->addSql("ALTER TABLE acts_performances DROP FOREIGN KEY FK_E317F2D4E176C6");
        $this->addSql("DROP INDEX `IDX_E317F2D457167AB4` ON `acts_performances`");
        $this->addSql("DROP INDEX `IDX_E317F2D4E176C6` ON `acts_performances`");
        $this->addSql("ALTER TABLE `acts_performances` CHANGE `sid` sid INT DEFAULT 0 NOT NULL, CHANGE `venid` venid INT DEFAULT 0 NOT NULL, CHANGE `startdate` startdate DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `enddate` enddate DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `excludedate` excludedate DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `time` time TIME DEFAULT '00:00:00' NOT NULL");
        $this->addSql("ALTER TABLE acts_reviews DROP FOREIGN KEY FK_4F8219D8592D0E6F");
        $this->addSql("ALTER TABLE acts_reviews DROP FOREIGN KEY FK_4F8219D8539B0606");
        $this->addSql("DROP INDEX `IDX_4F8219D8592D0E6F` ON `acts_reviews`");
        $this->addSql("DROP INDEX `IDX_4F8219D8539B0606` ON `acts_reviews`");
        $this->addSql("ALTER TABLE `acts_reviews` CHANGE `showid` showid INT DEFAULT 0 NOT NULL, CHANGE `uid` uid INT DEFAULT 0 NOT NULL, CHANGE `created` created DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
        $this->addSql("ALTER TABLE `acts_search_cache` CHANGE `obsolete` obsolete TINYINT(1) DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE `acts_shows` CHANGE `excludedate` excludedate DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `techsend` techsend TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `actorsend` actorsend TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `socid` socid INT DEFAULT 0 NOT NULL, CHANGE `venid` venid INT DEFAULT 0 NOT NULL, CHANGE `authorizeid` authorizeid INT DEFAULT 0 NOT NULL, CHANGE `entered` entered TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `entryexpiry` entryexpiry DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `category` category VARCHAR(255) DEFAULT 'other' NOT NULL, CHANGE `primaryref` primaryref INT DEFAULT 0 NOT NULL, CHANGE `timestamp` timestamp DATETIME NOT NULL");
        $this->addSql("CREATE UNIQUE INDEX id ON acts_shows (id)");
        $this->addSql("ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E57167AB4");
        $this->addSql("ALTER TABLE acts_shows_people_link DROP FOREIGN KEY FK_2F5AB85E5550C4ED");
        $this->addSql("ALTER TABLE `acts_shows_people_link` CHANGE `sid` sid INT DEFAULT 0 NOT NULL, CHANGE `pid` pid INT DEFAULT 0 NOT NULL, CHANGE `type` type VARCHAR(255) DEFAULT 'cast' NOT NULL, CHANGE `order` `order` INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE acts_shows_refs DROP FOREIGN KEY FK_86C0B071592D0E6F");
        $this->addSql("DROP INDEX `IDX_86C0B071592D0E6F` ON `acts_shows_refs`");
        $this->addSql("ALTER TABLE `acts_shows_refs` CHANGE `showid` showid INT DEFAULT 0 NOT NULL");
        $this->addSql("ALTER TABLE `acts_societies` CHANGE `type` type TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `affiliate` affiliate TINYINT(1) DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE `acts_support` CHANGE `supportid` supportid INT DEFAULT 0 NOT NULL, CHANGE `ownerid` ownerid INT DEFAULT 0 NOT NULL, CHANGE `state` state VARCHAR(255) DEFAULT 'unassigned' NOT NULL, CHANGE `datetime` datetime DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
        $this->addSql("ALTER TABLE `acts_techies` DROP INDEX `IDX_4D00DAC2592D0E6F`, ADD UNIQUE INDEX `showid` (`showid`)");
        $this->addSql("ALTER TABLE acts_techies DROP FOREIGN KEY FK_4D00DAC2592D0E6F");
        $this->addSql("ALTER TABLE `acts_techies` CHANGE `showid` showid INT NULL, CHANGE `deadline` deadline TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `expiry` expiry DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `display` display TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `remindersent` remindersent TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `lastupdated` lastupdated DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
        $this->addSql("ALTER TABLE `acts_techies_positions` DROP PRIMARY KEY, DROP id, CHANGE `orderid` orderid DOUBLE PRECISION DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE `acts_termdates` DROP INDEX `primary`, ADD INDEX `id` (`id`)");
        $this->addSql("ALTER TABLE `acts_termdates` CHANGE `startdate` startdate DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `enddate` enddate DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `firstweek` firstweek TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `lastweek` lastweek TINYINT(1) DEFAULT '8' NOT NULL, CHANGE `displayweek` displayweek TINYINT(1) DEFAULT '1' NOT NULL");
        $this->addSql("ALTER TABLE `acts_users` CHANGE `registered` registered DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `login` login DATE DEFAULT '0000-00-00' NOT NULL, CHANGE `contact` contact TINYINT(1) DEFAULT '1' NOT NULL, CHANGE `alumni` alumni TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `publishemail` publishemail TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `dbemail` dbemail TINYINT(1) DEFAULT '0' NOT NULL, CHANGE `dbphone` dbphone TINYINT(1) DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE `footprints` DROP INDEX `primary`, ADD INDEX `from` (`from`)");
        $this->addSql("ALTER TABLE `footprints` CHANGE `from` `from` INT DEFAULT 0 NOT NULL, CHANGE `to` `to` INT DEFAULT 0 NOT NULL, CHANGE `time` time INT DEFAULT 0 NOT NULL");

        $this->addSql('ALTER TABLE  `acts_access` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_applications` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_auditions` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_authtokens` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_catalogue` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_config` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_email` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_email_aliases` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_email_items` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_email_sigs` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_events` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_externalsites` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_forums` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_forums_messages` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_includes` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_keywords` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_knowledgebase` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_mailinglists` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_mailinglists_members` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_pages` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_pendingaccess` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_people_data` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_performances` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_reviews` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_shows_people_link` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_search_cache` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_shows` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql("CREATE FULLTEXT INDEX title ON acts_shows (title)");
        $this->addSql('ALTER TABLE  `acts_shows_refs` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_societies` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_stores` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_support` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_techies` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_techies_positions` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_termdates` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `acts_users` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER TABLE  `footprints` ENGINE = MYISAM, CHARACTER SET latin1 COLLATE latin1_swedish_ci');
        $this->addSql('ALTER DATABASE  `'.$this->connection->getDatabase().'`  CHARACTER SET latin1 COLLATE latin1_swedish_ci');
    }
}
