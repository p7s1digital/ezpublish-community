<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130710095807 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $this->installPackage(
            'myvideo_imageteaser_and_video_tracking',
            'myvideo_imageteaser_and_video_tracking-1.0-1.ezpkg'
        );

        $this->addSql("SELECT 'Installed package myvideo_imageteaser_and_video_tracking';");

    }

    public function down(Schema $schema)
    {
        $this->uninstallPackage('myvideo_imageteaser_and_video_tracking');

        $this->addSql("SELECT 'Uninstalled package myvideo_imageteaser_and_video_tracking';");
    }
}

