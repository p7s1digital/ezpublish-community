<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20130910104555 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_image_gallery', 'myvideo_image_gallery-1.1-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Gallery - Added field name';" )
        );

    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_image_gallery');
    }
}
