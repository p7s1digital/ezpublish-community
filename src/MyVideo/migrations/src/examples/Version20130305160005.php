<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Migration of basic MyVideo video content
 */
class Version20130305160005 extends eZPublishPackageMigration
{
    // video objects in video-media node
    protected $videos = array(
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8312598',
            'attributes' => array(
                'video_id' => '8312598',
                'title' => 'Hier ein individueller Titel für Nr. 8312598'
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8312596',
            'attributes' => array(
                'video_id' => '8312596',
                'title' => 'Hier ein individueller Titel für Nr. 8312596'
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8312590',
            'attributes' => array(
                'video_id' => '8312590',
                'title' => ''
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8312592',
            'attributes' => array(
                'video_id' => '8312592',
                'title' => ''
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8312593',
            'attributes' => array(
                'video_id' => '8312593',
                'title' => ''
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8588485',
            'attributes' => array(
                'video_id' => '8588485',
                'title' => ''
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8802379',
            'attributes' => array(
                'video_id' => '8802379',
                'title' => ''
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8692907',
            'attributes' => array(
                'video_id' => '8692907',
                'title' => ''
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8311041',
            'attributes' => array(
                'video_id' => '8311041',
                'title' => ''
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8309975',
            'attributes' => array(
                'video_id' => '8309975',
                'title' => ''
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8312501',
            'attributes' => array(
                'video_id' => '8312501',
                'title' => ''
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-7746453',
            'attributes' => array(
                'video_id' => '7746453',
                'title' => ''
            )
        ),
        array(
            'parent_node_id' => 'myvideo-video-import-target',
            'class_identifier' => 'myvideo_video',
            'remote_id' => 'myvideo-video-8683128',
            'attributes' => array(
                'video_id' => '8683128',
                'title' => ''
            )
        )
    );

    /**
     * upgrade ez-db with import of video content
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->videos AS $video) {
            //create and publish tree node
            $success = $this->createTreeNode( $video );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Video \"%s\" imported in parent_node \"%s\".';",
                        $video['attributes']['video_id'],
                        $video['parent_node_id']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Video \"%s\" was not imported in parent_node \"%s\".';",
                        $video['attributes']['video_id'],
                        $video['parent_node_id']
                    )
                );
            }
        }

        //shutdown script
        $this->script->shutdown();
    }

    /**
     * downgrade ez-db with removal of video content
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        foreach( $this->videos AS $video) {
            // remove folders
            $success = $this->removeTreeNode($video['remote_id'] );

            if ( $success ) {
                $this->addSql(
                    sprintf(
                        "SELECT 'Success: Video \"%s\" was removed.';",
                        $video['attributes']['video_id']
                    )
                );
            } else {
                $this->addSql(
                    sprintf(
                        "SELECT 'Error: Video \"%s\" was not removed.';",
                        $video['attributes']['video_id']
                    )
                );
            }
        }

        $this->script->shutdown();
    }
}
