<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191017203808 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_authtokens DROP FOREIGN KEY FK_11BF9FFBFCF7805F');
        $this->addSql('DROP TABLE acts_authtokens');
        $this->addSql('DROP TABLE acts_externalsites');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acts_authtokens (id INT AUTO_INCREMENT NOT NULL, siteid INT NOT NULL, userid INT NOT NULL, token VARCHAR(50) NOT NULL, issued DATETIME NOT NULL, INDEX IDX_11BF9FFBFCF7805F (siteid), INDEX IDX_11BF9FFBF132696E (userid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_externalsites (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_authtokens ADD CONSTRAINT FK_11BF9FFBFCF7805F FOREIGN KEY (siteid) REFERENCES acts_externalsites (id)');
        $this->addSql('ALTER TABLE acts_authtokens ADD CONSTRAINT FK_11BF9FFBF132696E FOREIGN KEY (userid) REFERENCES acts_users (id)');
    }
}
