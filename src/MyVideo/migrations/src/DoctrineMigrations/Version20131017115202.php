<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131017115202 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_sushibar', 'myvideo_sushibar-1.3-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Sushibar - Changed to MyVideoIcon';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_sushibar');
    }
}
