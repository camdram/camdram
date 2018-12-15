<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181215211316 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_users ADD last_session_at DATETIME DEFAULT NULL');

        $this->addSql('UPDATE acts_users SET last_session_at = last_login_at');
        $this->addSql('UPDATE acts_users AS u SET last_login_at = 
            GREATEST(u.last_password_login_at, u.last_login_at, 
                (SELECT MAX(e.last_login_at) FROM acts_external_users AS e WHERE e.user_id = u.id))');
        $this->addSql('UPDATE acts_users SET last_login_at = last_session_at WHERE last_login_at IS NULL');

        $this->addSql('ALTER TABLE acts_users DROP pass, DROP last_password_login_at');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_users ADD pass VARCHAR(32) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE last_session_at last_password_login_at DATETIME DEFAULT NULL');
    }
}
