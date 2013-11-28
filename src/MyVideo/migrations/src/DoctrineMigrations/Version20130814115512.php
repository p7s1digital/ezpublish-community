<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130814115512 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_tagpage', 'myvideo_tagpage-1.6.1-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Tagpage - added related tags module.';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_tagpage');
    }
}
