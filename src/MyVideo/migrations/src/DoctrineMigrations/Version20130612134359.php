<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130612134359 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_partner_campaign', 'myvideo_partner_campaign-1.0-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Partner Campaign Class added';" )
        );
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_partner_campaign');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Partner Campaign Class removed';" )
        );
    }
}
