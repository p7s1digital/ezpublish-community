<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Migration class initializing multiple folders
 */
class Version20130305160004 extends eZPublishPackageMigration
{
    /**
     * tree-node configuration for folder-structure in MyVideo
     *
     * @var array $nodes
     */
    protected $nodes = array(
        // folders under media-content node
        array(
            'parent_node_id' => 43,
            'class_identifier' => 'folder',
            'remote_id' => 'myvideo-folder-myvideo',
            'attributes' => array(
                'name' => 'MyVideo',
                'short_name' => 'MyVideo',
                'description' => 'In diesem Ordner werden alle Medieninhalte gepflegt, die auf MyVideo verwendet weden.',
                'short_description' => 'Hier liegen die MyVideo Medien Inhalte.'
            ),
            'child_nodes' => array(
                array(
                    'remote_id' => 'myvideo-folder-sushibar-layouts',
                    'attributes' => array(
                        'name' => 'Sushibar-Layouts',
                        'short_name' => 'Sushibar-Layouts',
                        'description' => 'In diesem Unterordner werden alle Sushibar-Layouts gepflegt, die auf MyVideo verwendet weden.',
                        'short_description' => 'Hier liegen die MyVideo Sushibar-Layouts.'
                    )
                ),
                array(
                    'remote_id' => 'myvideo-folder-sushibars',
                    'attributes' => array(
                        'name' => 'Sushibars',
                        'short_name' => 'Sushibars',
                        'description' => 'In diesem Unterordner werden alle Sushibars gepflegt, die auf MyVideo verwendet weden.',
                        'short_description' => 'Hier liegen die MyVideo Sushibars.'
                    ),
                ),
                array(
                    'remote_id' => 'myvideo-video-import-target',
                    'attributes' => array(
                        'name' => 'Videos',
                        'short_name' => 'Videos',
                        'description' => 'In diesem Unterordner werden alle Videos gepflegt, die auf MyVideo verwendet weden.',
                        'short_description' => 'Hier liegen die MyVideo Videos.'
                    ),
                ),
                array(
                    'remote_id' => 'myvideo-folder-videolists',
                    'attributes' => array(
                        'name' => 'Videolisten',
                        'short_name' => 'Videolisten',
                        'description' => 'In diesem Unterordner werden alle Videolisten gepflegt, die auf MyVideo verwendet weden.',
                        'short_description' => 'Hier liegen die MyVideo Videolisten.'
                    ),
                ),
                array(
                    'remote_id' => 'myvideo-folder-solr-queries',
                    'attributes' => array(
                        'name' => 'SOLR-Abfragen',
                        'short_name' => 'SOLR',
                        'description' => 'In diesem Unterordner werden alle SOLR-Abfragen gepflegt, die auf MyVideo verwendet weden.',
                        'short_description' => 'Hier liegen die MyVideo SOLR-Abfragen.'
                    ),
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
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->nodes AS $node) {
            //create and publish tree node
            $success = $this->createTreeNode( $node );

            if ( $success ) {
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
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->nodes AS $node) {
            // remove folders
            $success = $this->removeTreeNode($node['remote_id'] );

            if ( $success ) {
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
