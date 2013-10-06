<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131006025627 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE footprints");
        $this->addSql("DROP INDEX slugs ON acts_entities");
        $this->addSql("CREATE UNIQUE INDEX slugs ON acts_entities (slug)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_D470A3A45F37A13B ON acts_auth_codes (token)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_75B14F685F37A13B ON acts_access_tokens (token)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1A3F91E75F37A13B ON acts_refresh_tokens (token)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("DROP INDEX UNIQ_75B14F685F37A13B ON acts_access_tokens");
        $this->addSql("DROP INDEX UNIQ_D470A3A45F37A13B ON acts_auth_codes");
        $this->addSql("DROP INDEX slugs ON acts_entities");
        $this->addSql("CREATE UNIQUE INDEX slugs ON acts_entities (entity_type, slug)");
        $this->addSql("DROP INDEX UNIQ_1A3F91E75F37A13B ON acts_refresh_tokens");
    }
}
