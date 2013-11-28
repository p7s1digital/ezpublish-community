<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130903181446 extends eZPublishPackageMigration
{
    // sushibar-layout objects in sushibar-layout node
    protected $layouts = array(
        array(
            'parent_node_id' => 'myvideo-folder-sushibar-layouts',
            'class_identifier' => 'myvideo_sushibar_layout',
            'remote_id' => 'myvideo-sushibar-layout-eight-small',
            'attributes' => array(
                'title' => 'Eight Small',
                'identifier' => 'eight-small',
                'description' => 'Zwei Reihen mit je vier Bildern.',
                'image' => array(
                    'src' => 'preview_one-big-four-small.png',
                    'title' => 'Standard 1'
                )
            )
        )
    );

    /**
     * upgrade ez-db with import of videolists
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->layouts AS $layout) {
            //create and publish tree node
            $success = $this->createTreeNode( $layout );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Sushibar layout \"%s\" imported in parent_node \"%s\".';",
                        $layout['attributes']['title'],
                        $layout['parent_node_id']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Sushibar layout \"%s\" was not imported in parent_node \"%s\".';",
                        $layout['attributes']['title'],
                        $layout['parent_node_id']
                    )
                );
            }
        }

        //shutdown script
        $this->script->shutdown();
    }

    /**
     * downgrade ez-db with removal of sushibar layout
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->layouts AS $layout) {
            // remove folders
            $success = $this->removeTreeNode( $layout['remote_id'] );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Sushibar layout \"%s\" was removed.';",
                        $layout['attributes']['title']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Sushibar layout \"%s\" was not removed.';",
                        $layout['attributes']['title']
                    )
                );
            }
        }

        $this->script->shutdown();
    }
}
