<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20131203152922 extends eZPublishPackageMigration
{

    private $package = array(
        'content_class_id' => 'myvideo_nice_url',
        'file' => 'myvideo_nice_url-1.1-1.ezpkg',
        'description' => 'MyVideo Nice Url mit UGC-Flag',
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
