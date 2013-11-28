<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130704153350 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $rows = $this->getAllObsoleteContentClassAttributeIds();
        $this->deleteObsoleteContentClassAttribute($rows);
        $this->addSql(
            sprintf( "SELECT 'All obsolete attributes deleted!';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->addSql(
            sprintf( "SELECT 'Nothing to restore!';" )
        );
    }
}
