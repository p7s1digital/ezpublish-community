<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Added optional index field what define with playlist control the player
 */
class Version20131205102411 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_staticpage', 'myvideo_staticpage-1.1-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Static Page - Added required Flag for Content Field';")
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_staticpage');
    }
}
