<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Migration class initializing multiple folders
 */
class Version20130305160010 extends eZPublishPackageMigration
{
    /**
     * tree-node configuration for folder-structure in MyVideo
     *
     * @var array $nodes
     */
    protected $nodes = array(
        // folder "MyVideo" under main-content node
        array(
            'parent_node_id' => 2,
            'class_identifier' => 'folder',
            'remote_id' => 'myvideo_content_folder1',
            'attributes' => array(
                'name' => 'MyVideo',
                'short_name' => 'MyVideo',
                'description' => 'In diesem Ordner werden alle Inhalte gepflegt, die auf MyVideo verwendet weden.',
                'short_description' => 'Hier liegen die MyVideo Inhalte.'
            ),
            'child_nodes' => array(
                array(
                    'class_identifier' => 'myvideo_homepage',
                    'remote_id' => 'myvideo-homepage',
                    'attributes' => array(
                        'stage' => 'myvideo-example-videolist-1',
                        'sushibars' => 'myvideo-example-sushibar-1,myvideo-example-sushibar-2'
                    )
                ),
                array(
                    'class_identifier' => 'folder',
                    'remote_id' => 'myvideo-content-folder-artikel',
                    'attributes' => array(
                        'name' => 'Artikel',
                        'short_name' => 'Artikel',
                        'description' => 'Hier liegen die MyVideo Artikel.',
                        'short_description' => 'Hier liegen die MyVideo Artikel.'
                    ),
                    'child_nodes' => array(
                        array(
                            'class_identifier' => 'article',
                            'remote_id' => 'myvideo-artikel-impressum',
                            'attributes' => array(
                                'title' => 'Impressum',
                                'intro' => 'Hier unser Impressum.'
                            )
                        )
                    )
                )
            )
        ),
        // folder "etc" under main-content node
        array(
            'parent_node_id' => 2,
            'class_identifier' => 'folder',
            'remote_id' => 'myvideo-content-folder-other',
            'attributes' => array(
                'name' => 'etc',
                'short_name' => 'etc',
                'description' => 'Hier liegen Hilfsobjekte, sowie Diverses und Verschiedenes.',
                'short_description' => 'Hier liegen Hilfsobjekte, sowie Diverses und Verschiedenes.'
            ),
            'child_nodes' => array(
                array(
                    'remote_id' => 'myvideo-content-folder-redirects',
                    'attributes' => array(
                        'name' => 'MyVideo Redirects',
                        'short_name' => 'MyVideo Redirects',
                        'description' => 'Hier liegen automatisch erstelle Redirect-Objekte.',
                        'short_description' => 'Hier liegen automatisch erstelle Redirect-Objekte.'
                    )
                ),
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
