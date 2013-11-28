<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130604110000 extends eZPublishPackageMigration
{
    public function up(Schema $schema)
    {
        $script = $this->getEzScriptInstance();

        //get parent sushibar folder node
        $parentNode = $this->getTreeNode('myvideo-folder-sushibar-layouts');

        //get all objects of class sushibar_layout to fetch dvd-cover(no remote-id,id available)
        $class = \eZContentClass::fetchByIdentifier('myvideo_sushibar_layout');
        $layouts = \eZContentObject::fetchSameClassList( $class->ID );
        foreach ($layouts as $layout) {
            if($layout->Name == 'DVD Cover') {
                // moving node to folder
                $nodeId = $layout->attribute('main_node_id');
                $newNodeId = $parentNode->attribute('main_node_id');
                \eZContentObjectTreeNodeOperations::move($nodeId, $newNodeId);
            }
        }

        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Sushibar DVD Cover moved to Sushibar Layout folder;'" )
        );

        $script->shutdown();
    }

    public function down(Schema $schema)
    {
        $script = $this->getEzScriptInstance();

        //get all objects of class sushibar_layout to fetch dvd-cover(no remote-id,id available)
        $class = \eZContentClass::fetchByIdentifier('myvideo_sushibar_layout');
        $layouts = \eZContentObject::fetchSameClassList( $class->ID );
        foreach ($layouts as $layout) {
            if($layout->Name == 'DVD Cover') {
                // moving node to folder
                $nodeId = $layout->attribute('main_node_id');
                \eZContentObjectTreeNodeOperations::move($nodeId, 2);
            }
        }

        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Sushibar DVD Cover moved back to Content folder;'" )
        );

        $script->shutdown();
    }
}
