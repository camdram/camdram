<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131225203348 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE acts_access_tokens DROP FOREIGN KEY FK_75B14F6819EB6921");
        $this->addSql("ALTER TABLE acts_auth_codes DROP FOREIGN KEY FK_D470A3A419EB6921");
        $this->addSql("ALTER TABLE acts_refresh_tokens DROP FOREIGN KEY FK_1A3F91E719EB6921");
        $this->addSql("CREATE TABLE acts_api_refresh_tokens (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, INDEX IDX_CACE640E19EB6921 (client_id), INDEX IDX_CACE640EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_api_access_tokens (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, INDEX IDX_E452518D19EB6921 (client_id), INDEX IDX_E452518DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_api_apps (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, organisation_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, is_admin TINYINT(1) NOT NULL, website VARCHAR(1024) NOT NULL, INDEX IDX_297ABD2CA76ED395 (us3er_id), INDEX IDX_297ABD2C9E6B1585 (organisation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_api_auth_codes (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, INDEX IDX_6FB69A2F19EB6921 (client_id), INDEX IDX_6FB69A2FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_api_refresh_tokens ADD CONSTRAINT FK_CACE640E19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)");
        $this->addSql("ALTER TABLE acts_api_refresh_tokens ADD CONSTRAINT FK_CACE640EA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_api_access_tokens ADD CONSTRAINT FK_E452518D19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)");
        $this->addSql("ALTER TABLE acts_api_access_tokens ADD CONSTRAINT FK_E452518DA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_api_apps ADD CONSTRAINT FK_297ABD2CA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_api_apps ADD CONSTRAINT FK_297ABD2C9E6B1585 FOREIGN KEY (organisation_id) REFERENCES acts_societies (id)");
        $this->addSql("ALTER TABLE acts_api_auth_codes ADD CONSTRAINT FK_6FB69A2F19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)");
        $this->addSql("ALTER TABLE acts_api_auth_codes ADD CONSTRAINT FK_6FB69A2FA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_support CHANGE ownerid ownerid INT DEFAULT NULL");
        $this->addSql("ALTER TABLE acts_support ADD CONSTRAINT FK_A6F6197275DAD987 FOREIGN KEY (ownerid) REFERENCES acts_users (id)");
        $this->addSql("CREATE INDEX IDX_A6F6197275DAD987 ON acts_support (ownerid)");
        $this->addSql("DROP TABLE acts_access_tokens");
        $this->addSql("DROP TABLE acts_auth_codes");
        $this->addSql("DROP TABLE acts_external_apps");
        $this->addSql("DROP TABLE acts_refresh_tokens");
        $this->addSql("ALTER TABLE acts_support ADD CONSTRAINT FK_A6F6197275DAD987 FOREIGN KEY (ownerid) REFERENCES acts_users (id)");
        $this->addSql("CREATE INDEX IDX_A6F6197275DAD987 ON acts_support (ownerid)");
        $this->addSql("ALTER TABLE acts_api_apps ADD random_id VARCHAR(255) NOT NULL, ADD redirect_uris LONGTEXT NOT NULL COMMENT '(DC2Type:array)', ADD secret VARCHAR(255) NOT NULL, ADD allowed_grant_types LONGTEXT NOT NULL COMMENT '(DC2Type:array)', ADD app_type VARCHAR(20) NOT NULL");
        $this->addSql('ALTER TABLE acts_api_apps ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql("CREATE TABLE acts_api_authorisations (externalapp_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_EF3B9B55C1F2BE32 (externalapp_id), INDEX IDX_EF3B9B55A76ED395 (user_id), PRIMARY KEY(externalapp_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_api_authorisations ADD CONSTRAINT FK_EF3B9B55C1F2BE32 FOREIGN KEY (externalapp_id) REFERENCES acts_api_apps (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acts_api_authorisations ADD CONSTRAINT FK_EF3B9B55A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE CASCADE");
        $this->addSql('UPDATE acts_support AS s LEFT JOIN acts_users AS u on s.ownerid = u.id SET s.ownerid = NULL WHERE u.id IS NULL');
        $this->addSql("ALTER TABLE acts_support ADD CONSTRAINT FK_A6F6197275DAD987 FOREIGN KEY (ownerid) REFERENCES acts_users (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("DROP TABLE acts_api_authorisations");
        $this->addSql("ALTER TABLE acts_support DROP FOREIGN KEY FK_A6F6197275DAD987");
        $this->addSql("ALTER TABLE acts_api_refresh_tokens DROP FOREIGN KEY FK_CACE640E19EB6921");
        $this->addSql("ALTER TABLE acts_api_access_tokens DROP FOREIGN KEY FK_E452518D19EB6921");
        $this->addSql("ALTER TABLE acts_api_auth_codes DROP FOREIGN KEY FK_6FB69A2F19EB6921");
        $this->addSql("CREATE TABLE acts_access_tokens (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, INDEX IDX_75B14F6819EB6921 (client_id), INDEX IDX_75B14F68A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_auth_codes (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, INDEX IDX_D470A3A419EB6921 (client_id), INDEX IDX_D470A3A4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_external_apps (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris LONGTEXT NOT NULL COMMENT '(DC2Type:array)', secret VARCHAR(255) NOT NULL, allowed_grant_types LONGTEXT NOT NULL COMMENT '(DC2Type:array)', name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, scope VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acts_refresh_tokens (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, INDEX IDX_1A3F91E719EB6921 (client_id), INDEX IDX_1A3F91E7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE acts_access_tokens ADD CONSTRAINT FK_75B14F6819EB6921 FOREIGN KEY (client_id) REFERENCES acts_external_apps (id)");
        $this->addSql("ALTER TABLE acts_access_tokens ADD CONSTRAINT FK_75B14F68A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_auth_codes ADD CONSTRAINT FK_D470A3A419EB6921 FOREIGN KEY (client_id) REFERENCES acts_external_apps (id)");
        $this->addSql("ALTER TABLE acts_auth_codes ADD CONSTRAINT FK_D470A3A4A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("ALTER TABLE acts_refresh_tokens ADD CONSTRAINT FK_1A3F91E719EB6921 FOREIGN KEY (client_id) REFERENCES acts_external_apps (id)");
        $this->addSql("ALTER TABLE acts_refresh_tokens ADD CONSTRAINT FK_1A3F91E7A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)");
        $this->addSql("DROP TABLE acts_api_refresh_tokens");
        $this->addSql("DROP TABLE acts_api_access_tokens");
        $this->addSql("DROP TABLE acts_api_apps");
        $this->addSql("DROP TABLE acts_api_auth_codes");
        $this->addSql("ALTER TABLE acts_support DROP FOREIGN KEY FK_A6F6197275DAD987");
        $this->addSql("DROP INDEX IDX_A6F6197275DAD987 ON acts_support");
        $this->addSql("ALTER TABLE acts_support CHANGE ownerid ownerid INT NOT NULL");
    }
}
