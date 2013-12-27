<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Added optional index field what define with playlist control the player
 */
class Version20131205151122 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_image_teaser', 'myvideo_image_teaser-3.2-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Image Teaser - Added Flag for hide Title';")
        );
        $this->installPackage('myvideo_video', 'myvideo_video-2.5-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Video - Added Flag for hide Title and PlayButton';")
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_image_teaser');
        $this->removePackage('myvideo_video');
    }
}
