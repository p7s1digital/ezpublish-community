<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;


class Version20131014123429 extends eZPublishPackageMigration
{

    private $package = array(
        'content_class_id' => 'myvideo_sub_homepage',
        'file' => 'myvideo_sub_homepage-1.0-1.ezpkg',
        'description' => 'MyVideo Sub Homepage',
    );

    private $editRole = array(
        'name' => 'Create and View MyVideo Sub Homepages',
        // array or comma-separated list
        'permissions' => 'create,read,edit,remove',
        // array or comma-separated list
        // @see parent::$groupNames for readable group names
        'groups' => 'EDITORS',
    );

    private $baseNode = array(
        'parent_node_id' => 2,
        'class_identifier' => 'folder',
        'remote_id' => 'myvideo-content-folder-sub-homepages',
        'attributes' => array(
            'name' => 'MyVideo Sub Homepages',
            'short_name' => 'MyVideo Sub Homepages',
            'description' => 'Sub Homepages für z.B. besondere Startseiten',
            'short_description' => 'Sub Homepages für z.B. besondere Startseiten',
        )
    );

    public function up(Schema $schema)
    {
        $this->installContentClass($this->package);

        $this->initScripting();
        $node = $this->createTreeNode($this->baseNode, true);
        $this->createRole($this->editRole, $this->package, $node);
        $this->shutdownScripting();
    }

    public function down(Schema $schema)
    {
        $this->initScripting();
        $this->removeTreeNode($this->baseNode['remote_id'], true);
        $this->removeRole($this->editRole);
        $this->shutdownScripting();

        $this->removeContentClass($this->package);
    }


}
