<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Added optional index field what define with playlist control the player
 */
class Version20131125174416 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_format_page', 'myvideo_format_page-1.7-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Format Page - Added Player Playcontrol Index field';")
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_format_page');
    }
}
