<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181125022959 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_show_slugs DROP FOREIGN KEY FK_476405A457167AB4');
        $this->addSql('ALTER TABLE acts_show_slugs ADD CONSTRAINT FK_476405A457167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE acts_show_slugs DROP FOREIGN KEY FK_476405A457167AB4');
        $this->addSql('ALTER TABLE acts_show_slugs ADD CONSTRAINT FK_476405A457167AB4 FOREIGN KEY (sid) REFERENCES acts_shows (id)');
    }
}
