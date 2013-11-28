<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130612134401 extends eZPublishPackageMigration
{

    protected $nodes = array(
        // folder "MyVideo Partner Aktionen" under main-content node
        array(
            'parent_node_id' => 2,
            'class_identifier' => 'folder',
            'remote_id' => 'myvideo-content-folder-partner-campaign',
            'attributes' => array(
                'name' => 'MyVideo Partner Aktionen',
                'short_name' => 'MyVideo Partner Aktionen',
                'description' => 'Aktionen der Partner von MyVideo.',
                'short_description' => 'Aktionen der Partner von MyVideo.'
            )
        )
    );

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
