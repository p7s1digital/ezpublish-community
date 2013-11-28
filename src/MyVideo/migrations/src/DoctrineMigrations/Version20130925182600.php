<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130925182600 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_tagpage', 'myvideo_tagpage-1.9-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Tagpage - removed Wikipedia-Tag field.';" )
        );

    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_tagpage');
    }
}
