<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130529150459 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_dvd_cover', 'myvideo_dvd_cover-1.0-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Sushibar DVD Cover import;'" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_dvd_cover');
    }
}
