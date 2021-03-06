<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130419163026 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        // installing package
        $this->installPackage('myvideo_image_teaser', 'myvideo_image_teaser-1.7-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Image Teaser - add the Date/Time Picker attributes to publishing or expriation';" )
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->removePackage('myvideo_image_teaser');
    }
}
