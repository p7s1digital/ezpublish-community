<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20130913121409 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_format_page', 'myvideo_format_page-1.1-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Format Page - Changed field values';" )
        );

    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_format_page');
    }
}
