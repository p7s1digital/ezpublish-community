<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

class Version20131119181010 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_image_teaser', 'myvideo_image_teaser-3.1-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Image Teaser - Added asNoFollow field';")
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_image_teaser');
    }
}
