<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131007115228 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_search', 'myvideo_search-1.4-1.ezpkg');
        $this->installPackage('myvideo_tagpage', 'myvideo_tagpage-2.0-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Search/Tagpage - SOLR add Suggestions and reference attribute to Boost Values;'" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_search');
        $this->removePackage('myvideo_tagpage');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Search/Tagpage - SOLR add Suggestions and reference attribute to Boost Values;'" )
        );
    }
}
