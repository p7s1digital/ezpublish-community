<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130419170554 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->installPackage('myvideo_image_teaser', 'myvideo_image_teaser-1.8-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Image Teaser - change the max char size of suschibar title and subtitle';" )
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->removePackage('myvideo_image_teaser');
    }
}
