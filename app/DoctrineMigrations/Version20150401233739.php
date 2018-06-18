<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150401233739 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acts_api_authorizations (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, scopes LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', INDEX IDX_7AA3DE8619EB6921 (client_id), INDEX IDX_7AA3DE86A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_api_authorizations ADD CONSTRAINT FK_7AA3DE8619EB6921 FOREIGN KEY (client_id) REFERENCES acts_api_apps (id)');
        $this->addSql('ALTER TABLE acts_api_authorizations ADD CONSTRAINT FK_7AA3DE86A76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)');
        $this->addSql('DROP TABLE acts_api_authorisations');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE acts_api_authorizations');
    }
}
