<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Migration of basic MyVideo solr queries
 */
class Version20130305160006 extends eZPublishPackageMigration
{
    // query objects in solr-media node
    protected $queries = array(
        array(
            'parent_node_id' => 'myvideo-folder-solr-queries',
            'class_identifier' => 'myvideo_solr_query',
            'remote_id' => 'myvideo-solr-query-1',
            'attributes' => array(
                'title' => 'Beispiel SOLR Query Nr.1',
                'description' => 'Hier wird die SOLR Query beschrieben.',
                'query' => 'example-solr-query'
            )
        ),
    );

    /**
     * upgrade ez-db with import of solr queries
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->queries AS $query) {
            //create and publish tree node
            $success = $this->createTreeNode( $query );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Solr query \"%s\" imported in parent_node \"%s\".';",
                        $query['attributes']['title'],
                        $query['parent_node_id']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Solr query \"%s\" was not imported in parent_node \"%s\".';",
                        $query['attributes']['title'],
                        $query['parent_node_id']
                    )
                );
            }
        }

        //shutdown script
        $this->script->shutdown();
    }

    /**
     * downgrade ez-db with removal of solr content
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->queries AS $query) {
            // remove folders
            $success = $this->removeTreeNode( $query['remote_id'] );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Solr query \"%s\" was removed.';",
                        $query['attributes']['title']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Video \"%s\" was not removed.';",
                        $query['attributes']['title']
                    )
                );
            }
        }

        $this->script->shutdown();
    }
}
