<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130809174801 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_search', 'myvideo_search-1.2-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Search - added the Boost input fields.';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_search');
    }
}
