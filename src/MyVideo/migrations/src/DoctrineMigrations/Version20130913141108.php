<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20130913141108 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_image_gallery', 'myvideo_image_gallery-1.2-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Gallery - Added field ad';" )
        );

    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_image_gallery');
    }
}
