<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180721185601 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `acts_access` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_api_apps` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_api_authorizations` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_applications` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_auditions` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_authtokens` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_events` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_externalsites` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_external_users` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_news` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_pendingaccess` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_people_data` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_performances` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_shows` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_shows_people_link` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_societies` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_techies` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_time_periods` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_users` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `acts_week_names` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `ext_log_entries` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE `images` CONVERT TO CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //Not implemented
    }
}
