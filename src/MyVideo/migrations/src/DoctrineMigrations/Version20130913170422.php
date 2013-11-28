<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130913170422 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_format_page', 'myvideo_format_page-1.2-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Format Page - Changed field values';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_format_page');
    }
}
