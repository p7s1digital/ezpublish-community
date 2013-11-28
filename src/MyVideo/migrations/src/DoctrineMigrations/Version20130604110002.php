<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130604110002 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_api_classes', 'myvideo_api_classes-1.0-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo API Classes imported;'" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_api_classes');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo API Classes removed;'" )
        );
    }
}
