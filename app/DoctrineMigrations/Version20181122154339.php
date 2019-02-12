<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181122154339 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_shows DROP FOREIGN KEY FK_1A1A53FE5FB42679');
        $this->addSql('DROP INDEX IDX_1A1A53FE5FB42679 ON acts_shows');
        $this->addSql('ALTER TABLE acts_shows ADD authorised TINYINT(1) NOT NULL');

        //Set new authorsied flag from authorizeid value
        $this->addSql('UPDATE acts_shows SET authorised = 1 WHERE authorizeid IS NOT NULL');

        $this->addSql('ALTER TABLE acts_shows DROP authorizeid');
        $this->addSql('ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB50643151C11F');
        $this->addSql('ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB50643151C11F FOREIGN KEY (granted_by_id) REFERENCES acts_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_pendingaccess DROP FOREIGN KEY FK_3EA48E146EEF703F');
        $this->addSql('ALTER TABLE acts_pendingaccess ADD CONSTRAINT FK_3EA48E146EEF703F FOREIGN KEY (issuerid) REFERENCES acts_users (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE acts_users DROP FOREIGN KEY FK_62A20753217BBB47');
        $this->addSql('ALTER TABLE acts_users ADD CONSTRAINT FK_62A20753217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_api_apps DROP FOREIGN KEY FK_297ABD2C9E6B1585');
        $this->addSql('ALTER TABLE acts_api_apps DROP FOREIGN KEY FK_297ABD2CA76ED395');
        $this->addSql('ALTER TABLE acts_api_apps ADD CONSTRAINT FK_297ABD2C9E6B1585 FOREIGN KEY (organisation_id) REFERENCES acts_societies (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_api_apps ADD CONSTRAINT FK_297ABD2CA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE acts_api_access_tokens DROP FOREIGN KEY FK_E452518D19EB6921');
        $this->addSql('ALTER TABLE acts_api_access_tokens DROP FOREIGN KEY FK_E452518DA76ED395');
        $this->addSql('ALTER TABLE acts_api_access_tokens CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE acts_api_access_tokens ADD CONSTRAINT FK_E452518D19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_api_access_tokens ADD CONSTRAINT FK_E452518DA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_api_refresh_tokens DROP FOREIGN KEY FK_CACE640E19EB6921');
        $this->addSql('ALTER TABLE acts_api_refresh_tokens DROP FOREIGN KEY FK_CACE640EA76ED395');
        $this->addSql('ALTER TABLE acts_api_refresh_tokens CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE acts_api_refresh_tokens ADD CONSTRAINT FK_CACE640E19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_api_refresh_tokens ADD CONSTRAINT FK_CACE640EA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_api_auth_codes DROP FOREIGN KEY FK_6FB69A2F19EB6921');
        $this->addSql('ALTER TABLE acts_api_auth_codes DROP FOREIGN KEY FK_6FB69A2FA76ED395');
        $this->addSql('ALTER TABLE acts_api_auth_codes CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE acts_api_auth_codes ADD CONSTRAINT FK_6FB69A2F19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_api_auth_codes ADD CONSTRAINT FK_6FB69A2FA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_api_authorizations DROP FOREIGN KEY FK_7AA3DE8619EB6921');
        $this->addSql('ALTER TABLE acts_api_authorizations DROP FOREIGN KEY FK_7AA3DE86A76ED395');
        $this->addSql('ALTER TABLE acts_api_authorizations CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE acts_api_authorizations ADD CONSTRAINT FK_7AA3DE8619EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acts_api_authorizations ADD CONSTRAINT FK_7AA3DE86A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_access DROP FOREIGN KEY FK_2DAB50643151C11F');
        $this->addSql('ALTER TABLE acts_access ADD CONSTRAINT FK_2DAB50643151C11F FOREIGN KEY (granted_by_id) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_pendingaccess DROP FOREIGN KEY FK_3EA48E146EEF703F');
        $this->addSql('ALTER TABLE acts_pendingaccess ADD CONSTRAINT FK_3EA48E146EEF703F FOREIGN KEY (issuerid) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_shows ADD authorizeid INT DEFAULT NULL, DROP authorised');
        $this->addSql('ALTER TABLE acts_shows ADD CONSTRAINT FK_1A1A53FE5FB42679 FOREIGN KEY (authorizeid) REFERENCES acts_users (id)');
        $this->addSql('CREATE INDEX IDX_1A1A53FE5FB42679 ON acts_shows (authorizeid)');
        $this->addSql('ALTER TABLE acts_users DROP FOREIGN KEY FK_62A20753217BBB47');
        $this->addSql('ALTER TABLE acts_users ADD CONSTRAINT FK_62A20753217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)');
        $this->addSql('ALTER TABLE acts_api_apps DROP FOREIGN KEY FK_297ABD2CA76ED395');
        $this->addSql('ALTER TABLE acts_api_apps DROP FOREIGN KEY FK_297ABD2C9E6B1585');
        $this->addSql('ALTER TABLE acts_api_apps ADD CONSTRAINT FK_297ABD2CA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_api_apps ADD CONSTRAINT FK_297ABD2C9E6B1585 FOREIGN KEY (organisation_id) REFERENCES acts_societies (id)');
        $this->addSql('ALTER TABLE acts_api_access_tokens DROP FOREIGN KEY FK_E452518D19EB6921');
        $this->addSql('ALTER TABLE acts_api_access_tokens DROP FOREIGN KEY FK_E452518DA76ED395');
        $this->addSql('ALTER TABLE acts_api_access_tokens CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_api_access_tokens ADD CONSTRAINT FK_E452518D19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)');
        $this->addSql('ALTER TABLE acts_api_access_tokens ADD CONSTRAINT FK_E452518DA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_api_auth_codes DROP FOREIGN KEY FK_6FB69A2F19EB6921');
        $this->addSql('ALTER TABLE acts_api_auth_codes DROP FOREIGN KEY FK_6FB69A2FA76ED395');
        $this->addSql('ALTER TABLE acts_api_auth_codes CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_api_auth_codes ADD CONSTRAINT FK_6FB69A2F19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)');
        $this->addSql('ALTER TABLE acts_api_auth_codes ADD CONSTRAINT FK_6FB69A2FA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_api_authorizations DROP FOREIGN KEY FK_7AA3DE8619EB6921');
        $this->addSql('ALTER TABLE acts_api_authorizations DROP FOREIGN KEY FK_7AA3DE86A76ED395');
        $this->addSql('ALTER TABLE acts_api_authorizations CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_api_authorizations ADD CONSTRAINT FK_7AA3DE8619EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)');
        $this->addSql('ALTER TABLE acts_api_authorizations ADD CONSTRAINT FK_7AA3DE86A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)');
        $this->addSql('ALTER TABLE acts_api_refresh_tokens DROP FOREIGN KEY FK_CACE640E19EB6921');
        $this->addSql('ALTER TABLE acts_api_refresh_tokens DROP FOREIGN KEY FK_CACE640EA76ED395');
        $this->addSql('ALTER TABLE acts_api_refresh_tokens CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_api_refresh_tokens ADD CONSTRAINT FK_CACE640E19EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)');
        $this->addSql('ALTER TABLE acts_api_refresh_tokens ADD CONSTRAINT FK_CACE640EA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)');
    }
}
