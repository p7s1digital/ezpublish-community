<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20131021145649 extends eZPublishPackageMigration
{

    private $tabModule = array(
        'content_class_id' => 'myvideo_tab_module',
        'file' => 'myvideo_tab_module-1.0-1.ezpkg',
        'description' => 'MyVideo Tab Module',
    );

    private $subHomepage = array(
        'content_class_id' => 'myvideo_sub_homepage',
        'file' => 'myvideo_sub_homepage-1.2-1.ezpkg',
        'description' => 'MyVideo Sub Homepage mit Tab Modul',
    );

    private $nodes = array(

        array(
            'parent_node_id' => 43,
            'class_identifier' => 'folder',
            'remote_id' => 'myvideo-content-folder-tab-module',
            'attributes' => array(
                'name' => 'Tab Module',
                'short_name' => 'Tab Module',
                'description' => 'Tab Module',
                'short_description' => 'Tab Module',
            )
        ),

    );

    public function up(Schema $schema)
    {
        $this->installContentClass($this->tabModule);
        $this->installContentClass($this->subHomepage);

        $this->initScripting();
        $this->createTreeNodes($this->nodes);
        $this->shutdownScripting();
    }

    public function down(Schema $schema)
    {
        $this->removeContentClass($this->subHomepage);
        $this->removeContentClass($this->tabModule);

        $this->initScripting();
        $this->removeTreeNodes($this->nodes);
        $this->shutdownScripting();
    }
}
