<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130605115328
    extends eZPublishPackageMigration
{
    protected $nodes = array(
        // folder "MyVideo Konfiguration" under main-content node
        array(
            'parent_node_id' => 2,
            'class_identifier' => 'folder',
            'remote_id' => 'myvideo-content-folder-config',
            'attributes' => array(
                'name' => 'MyVideo Konfiguration',
                'short_name' => 'MyVideo Konfiguration',
                'description' => 'Hier liegen Konfigurationsobjekte.',
                'short_description' => 'Hier liegen Konfigurationsobjekte.'
            ),
            'child_nodes' => array(
                array(
                    'class_identifier' => 'myvideo_config',
                    'remote_id' => 'myvideo-config',
                    'attributes' => array(
                        'stage_rotation_delay' => '1000',
                        'stage_rotation_speed' => '1000'
                    )
                )
            )
        )
    );

    /**
     * upgrade ez-db with import of folders
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        foreach ($this->nodes AS $node) {
            //create and publish tree node
            $success = $this->createTreeNode($node);

            if ($success) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Folder \"%s\" imported in parent_node \"%s\".';",
                        $node['attributes']['name'],
                        $node['parent_node_id']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Folder \"%s\" was not imported in parent_node \"%s\".';",
                        $node['attributes']['name'],
                        $node['parent_node_id']
                    )
                );
            }
        }

        //shutdown script
        $this->script->shutdown();
    }

    /**
     * downgrade ez-db with removal of folders
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        foreach ($this->nodes AS $node) {
            // remove folders
            $success = $this->removeTreeNode($node['remote_id']);

            if ($success) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Folder \"%s\" with sub-folders were removed.';",
                        $node['attributes']['name']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Folder \"%s\" with sub-folders were not removed.';",
                        $node['attributes']['name']
                    )
                );
            }
        }

        $this->script->shutdown();
    }
}
