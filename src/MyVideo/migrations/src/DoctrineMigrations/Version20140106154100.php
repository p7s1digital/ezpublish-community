<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Added optional index field what define with playlist control the player
 */
class Version20140106154100 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_image_gallery', 'myvideo_image_gallery-1.3-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Image Gallery - Added new Upload Field to Image Gallery';")
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_image_gallery');
    }
}
