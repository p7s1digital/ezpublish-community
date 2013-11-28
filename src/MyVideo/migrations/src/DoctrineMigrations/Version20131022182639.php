<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131022182639 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_sub_homepage', 'myvideo_sub_homepage-1.3-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo SubHomepage - delete obsolete share title attribute and activated tab and text module.';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_sub_homepage');
    }
}
