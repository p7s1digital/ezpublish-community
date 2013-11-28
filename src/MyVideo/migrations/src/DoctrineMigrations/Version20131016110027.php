<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20131016110027 extends eZPublishPackageMigration
{
    private $package = array(
        'content_class_id' => 'myvideo_sub_homepage',
        'file' => 'myvideo_sub_homepage-1.1-1.ezpkg',
        'description' => 'MyVideo Sub Homepage',
    );

    public function up(Schema $schema)
    {
        $this->installContentClass($this->package);
    }

    public function down(Schema $schema)
    {
        $this->removeContentClass($this->package);
    }
}
