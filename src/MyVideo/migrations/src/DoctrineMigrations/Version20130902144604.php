<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130902144604 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_menu', 'myvideo_menu-1.0-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Menu - added the Welcome Screen attributes.';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_menu');
    }
}
