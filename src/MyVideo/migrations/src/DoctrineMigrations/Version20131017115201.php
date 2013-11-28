<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20131017115201 extends eZPublishPackageMigration
{

    private $package = array(
        'content_class_id' => 'myvideo_videoliste',
        'file' => 'myvideo_videoliste-1.0-1.ezpkg',
        'description' => 'MyVideo Videoliste',
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
