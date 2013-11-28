<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130628165810 extends eZPublishPackageMigration
{
    protected $footerNode = array(
        array(
            'parent_node_id' => 'myvideo_content_folder1',
            'class_identifier' => 'myvideo_footer',
            'remote_id' => 'myvideo_footer-tagpage',
            'attributes' => array(
                'title' => 'MyVideo Tagpage Footer',
                'copy' => 'Bitte noch editieren!',
                'redis_key' => 'Tagpage'
            )
        ),
    );

    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_footer', 'myvideo_footer-1.0-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Footer - add selection.';" )
        );

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        foreach( $this->footerNode AS $node) {
            //create and publish tree node
            $success = $this->createTreeNode( $node );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Solr query \"%s\" imported in parent_node \"%s\".';",
                        $node['attributes']['title'],
                        $node['parent_node_id']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Solr query \"%s\" was not imported in parent_node \"%s\".';",
                        $node['attributes']['title'],
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
        $this->removePackage('myvideo_footer');

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        foreach( $this->footerNode AS $node) {
            //create and publish tree node
            $success = $this->removeTreeNode( $node['remote_id'] );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: \"%s\" imported in parent_node \"%s\".';",
                        $node['attributes']['title'],
                        $node['parent_node_id']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: \"%s\" was not imported in parent_node \"%s\".';",
                        $node['attributes']['title'],
                        $node['parent_node_id']
                    )
                );
            }
        }

        //shutdown script
        $this->script->shutdown();
    }
}
