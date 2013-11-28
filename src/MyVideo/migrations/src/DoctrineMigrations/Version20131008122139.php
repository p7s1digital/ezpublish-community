<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131008122139 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_solr_boost', 'myvideo_solr_boost-1.1-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo SOLR Boost - fix typing error';")
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_solr_boost');
    }
}
