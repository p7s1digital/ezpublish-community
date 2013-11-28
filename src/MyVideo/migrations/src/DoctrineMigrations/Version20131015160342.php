<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20131015160342 extends eZPublishPackageMigration
{

    private $package = array(
        'content_class_id' => 'myvideo_text_teaser',
        'file' => 'myvideo_text_teaser-1.0-1.ezpkg',
        'description' => 'MyVideo Text Teaser',
    );

    private $baseNode = array(
        'parent_node_id' => 43,
        'class_identifier' => 'folder',
        'remote_id' => 'myvideo-content-folder-text-teaser',
        'attributes' => array(
            'name' => 'Text Teaser',
            'short_name' => 'Text Teaser',
            'description' => 'Text Teaser für Sushibars',
            'short_description' => 'Text Teaser für Sushibars',
        )
    );

    public function up(Schema $schema)
    {
        $this->installContentClass($this->package);

        $this->initScripting();
        $this->createTreeNode($this->baseNode, true);
        $this->shutdownScripting();
    }

    public function down(Schema $schema)
    {
        $this->initScripting();
        $this->removeTreeNode($this->baseNode['remote_id'], true);
        $this->shutdownScripting();

        $this->removeContentClass($this->package);
    }
}
