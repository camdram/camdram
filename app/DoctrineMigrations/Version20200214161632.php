<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200214161632 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE acts_external_users AS e JOIN acts_users AS u ON e.email = u.email SET e.user_id = u.id WHERE e.user_ID IS NULL');
        $this->addSql('DELETE FROM acts_external_users WHERE user_id IS NULL');

        $this->addSql('ALTER TABLE acts_external_users DROP FOREIGN KEY FK_A75DA06C217BBB47');
        $this->addSql('ALTER TABLE acts_external_users DROP FOREIGN KEY FK_A75DA06CA76ED395');
        $this->addSql('DROP INDEX IDX_A75DA06C217BBB47 ON acts_external_users');
        $this->addSql('ALTER TABLE acts_external_users DROP person_id, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE acts_external_users ADD CONSTRAINT FK_A75DA06CA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_external_users DROP FOREIGN KEY FK_A75DA06CA76ED395');
        $this->addSql('ALTER TABLE acts_external_users ADD person_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE acts_external_users ADD CONSTRAINT FK_A75DA06C217BBB47 FOREIGN KEY (person_id) REFERENCES acts_people_data (id)');
        $this->addSql('CREATE INDEX IDX_A75DA06C217BBB47 ON acts_external_users (person_id)');
        $this->addSql('ALTER TABLE acts_external_users ADD CONSTRAINT FK_A75DA06CA76ED395 FOREIGN KEY (user_id) REFERENCES acts_users (id)');
    }
}
