<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Migration of basic MyVideo videolists
 */
class Version20130305160007 extends eZPublishPackageMigration
{
    // videolist objects in videolist node
    protected $videolists = array(
        array(
            'parent_node_id' => 'myvideo-folder-videolists',
            'class_identifier' => 'myvideo_videolist',
            'remote_id' => 'myvideo-example-videolist-1',
            'attributes' => array(
                'title' => 'Beispiel Videoliste Nr.1',
                'description' => 'Hier wird die Videoliste beschrieben.',
                'type' => 'manuell',
                'relation_videos' => 'myvideo-video-8312598,myvideo-video-8312596,myvideo-video-8312590',
                'querylist' => 'myvideo-solr-query-1'
            )
        ),
        array(
            'parent_node_id' => 'myvideo-folder-videolists',
            'class_identifier' => 'myvideo_videolist',
            'remote_id' => 'myvideo-example-videolist-2',
            'attributes' => array(
                'title' => 'Beispiel Videoliste Nr.2',
                'description' => 'Hier wird die Videoliste beschrieben.',
                'type' => 'dynamisch',
                'querylist' => 'myvideo-solr-query-1'
            )
        ),
        array(
            'parent_node_id' => 'myvideo-folder-videolists',
            'class_identifier' => 'myvideo_videolist',
            'remote_id' => 'myvideo-example-videolist-3',
            'attributes' => array(
                'title' => 'Beispiel Videoliste Nr.3',
                'description' => 'Hier wird die Videoliste beschrieben.',
                'type' => 'kombiniert',
                'relation_videos' => 'myvideo-video-8312598,myvideo-video-8312596,myvideo-video-8312590',
                'querylist' => 'myvideo-solr-query-1'
            )
        ),
        array(
            'parent_node_id' => 'myvideo-folder-videolists',
            'class_identifier' => 'myvideo_videolist',
            'remote_id' => 'myvideo-example-videolist-4',
            'attributes' => array(
                'title' => 'Beispiel Videoliste Nr.4',
                'description' => 'Hier wird die Videoliste beschrieben.',
                'type' => 'Chart',
                'chart_id' => '789'
            )
        ),
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

        foreach( $this->videolists AS $videolist) {
            //create and publish tree node
            $success = $this->createTreeNode( $videolist );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Videolist \"%s\" imported in parent_node \"%s\".';",
                        $videolist['attributes']['title'],
                        $videolist['parent_node_id']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Videolist \"%s\" was not imported in parent_node \"%s\".';",
                        $videolist['attributes']['title'],
                        $videolist['parent_node_id']
                    )
                );
            }
        }

        //shutdown script
        $this->script->shutdown();
    }

    /**
     * downgrade ez-db with removal of videolists
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->videolists AS $videolist) {
            // remove folders
            $success = $this->removeTreeNode( $videolist['remote_id'] );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Videolist \"%s\" was removed.';",
                        $videolist['attributes']['title']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Videolist \"%s\" was not removed.';",
                        $videolist['attributes']['title']
                    )
                );
            }
        }

        $this->script->shutdown();
    }
}
