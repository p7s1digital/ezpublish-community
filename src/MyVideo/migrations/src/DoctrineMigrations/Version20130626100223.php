<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130626100223 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_image_teaser', 'myvideo_image_teaser-2.9-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Image Teaser - add the gradient flag.';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_image_teaser');
    }
}
