<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130815162751 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE myvideometadata ADD image_url varchar(255);");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE myvideometadata DROP IF EXISTS image_url;");
    }
}
