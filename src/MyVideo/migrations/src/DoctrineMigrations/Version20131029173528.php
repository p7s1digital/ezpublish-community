<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131029173528 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_contest_page', 'myvideo_contest_page-1.1-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Contest Page - 2.version installation';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_contest_page');
    }
}
