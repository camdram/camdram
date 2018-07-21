<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180721140227 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE acts_news_links');
        $this->addSql('DROP TABLE acts_news_mentions');
        $this->addSql('ALTER TABLE acts_news DROP num_comments, DROP num_likes, DROP public');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acts_news_links (id INT AUTO_INCREMENT NOT NULL, news_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, link VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, source VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, media_type VARCHAR(20) DEFAULT NULL COLLATE utf8_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, picture VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_56C215DAB5A459A0 (news_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE acts_news_mentions (id INT AUTO_INCREMENT NOT NULL, entity_id INT DEFAULT NULL, news_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, remote_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, service VARCHAR(20) NOT NULL COLLATE utf8_unicode_ci, offset INT NOT NULL, length INT NOT NULL, INDEX IDX_9A671BDAB5A459A0 (news_id), INDEX IDX_9A671BDA81257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_news_links ADD CONSTRAINT FK_56C215DAB5A459A0 FOREIGN KEY (news_id) REFERENCES acts_news (id)');
        $this->addSql('ALTER TABLE acts_news_mentions ADD CONSTRAINT FK_9A671BDA81257D5D FOREIGN KEY (entity_id) REFERENCES acts_societies (id)');
        $this->addSql('ALTER TABLE acts_news_mentions ADD CONSTRAINT FK_9A671BDAB5A459A0 FOREIGN KEY (news_id) REFERENCES acts_news (id)');
        $this->addSql('ALTER TABLE acts_news ADD num_comments INT DEFAULT NULL, ADD num_likes INT DEFAULT NULL, ADD public TINYINT(1) NOT NULL');
    }
}
