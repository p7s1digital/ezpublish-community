<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Migration of basic MyVideo sushibars
 */
class Version20130305160009 extends eZPublishPackageMigration
{
    // sushibar objects in sushibar node
    protected $sushibars = array(
        array(
            'parent_node_id' => 'myvideo-folder-sushibars',
            'class_identifier' => 'myvideo_sushibar',
            'remote_id' => 'myvideo-example-sushibar-1',
            'attributes' => array(
                'title' => 'Beispiel Sushibar Nr. 1',
                'relation_layout' => 'myvideo-sushibar-layout-one-big-four-small',
                'relation_videolist' => 'myvideo-example-videolist-1'
            )
        ),
        array(
            'parent_node_id' => 'myvideo-folder-sushibars',
            'class_identifier' => 'myvideo_sushibar',
            'remote_id' => 'myvideo-example-sushibar-2',
            'attributes' => array(
                'title' => 'Beispiel Sushibar Nr. 2',
                'relation_layout' => 'myvideo-sushibar-layout-four-small',
                'relation_videolist' => 'myvideo-example-videolist-2'
            )
        )
    );

    /**
     * upgrade ez-db with import of sushibars
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->sushibars AS $sushibar) {
            //create and publish tree node
            $success = $this->createTreeNode( $sushibar );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Sushibar \"%s\" imported in parent_node \"%s\".';",
                        $sushibar['attributes']['title'],
                        $sushibar['parent_node_id']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Sushibar \"%s\" was not imported in parent_node \"%s\".';",
                        $sushibar['attributes']['title'],
                        $sushibar['parent_node_id']
                    )
                );
            }
        }

        //shutdown script
        $this->script->shutdown();
    }

    /**
     * downgrade ez-db with removal of sushibars
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->sushibars AS $sushibar) {
            // remove folders
            $success = $this->removeTreeNode( $sushibar['remote_id'] );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Sushibar \"%s\" was removed.';",
                        $sushibar['attributes']['title']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Sushibar \"%s\" was not removed.';",
                        $sushibar['attributes']['title']
                    )
                );
            }
        }

        $this->script->shutdown();
    }
}
