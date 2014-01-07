<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Added optional index field what define with playlist control the player
 */
class Version20140107000000 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_image_teaser_direct_link', 'myvideo_image_teaser-3.3-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Image Teaser - Added direct link controll box';")
        );

    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_image_teaser');
        $this->removePackage('myvideo_video');
    }
}
