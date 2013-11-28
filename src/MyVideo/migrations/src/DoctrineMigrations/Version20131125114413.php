<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131125114413 extends eZPublishPackageMigration
{
    const ROLE_NAME = 'Create and View MyVideo SocialMedia Comments';
    const CONTENT_CLASS_IDENTIFIER = 'myvideo_socialmedia_comments';
    const USER_GROUP_EDITORS_REMOTE_ID = 'myvideo-user_group-3';

    //parent folder configuration
    protected $sozialMediaFolders = array(
        'parent_node_id' => 43,
        'class_identifier' => 'folder',
        'remote_id' => 'myvideo-socialmedia',
        'attributes' => array (
            'name' => 'MyVideo SocialMedia',
            'short_name' => 'MyVideo SocialMedia',
            'description' => 'MyVideo SocialMedia',
            'short_description' => 'MyVideo SocialMedia'
        )
    );

    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_sub_homepage', 'myvideo_sub_homepage-1.4-1.ezpkg');
        $this->installPackage('myvideo_format_page', 'myvideo_format_page-1.6-1.ezpkg');
        $this->installPackage(static::CONTENT_CLASS_IDENTIFIER, 'myvideo_socialmedia_comments-1.0-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo SocialMedia comment modul';")
        );

        //create parent folder
        $sozialMediaFolderNode = $this->createTreeNode($this->sozialMediaFolders);

        if ($sozialMediaFolderNode) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: Folder \"%s\" imported in parent_node \"%s\".';",
                    $this->sozialMediaFolders['attributes']['name'],
                    $this->sozialMediaFolders['parent_node_id']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Folder \"%s\" was not imported in parent_node \"%s\".';",
                    $this->sozialMediaFolders['attributes']['name'],
                    $this->sozialMediaFolders['parent_node_id']
                )
            );
        }

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        // CREATE ROLE /////////////////////////////////////////////////////////////////////////////////////////////////

        // we need the content class, because we need its ID
        $contentClass = \eZContentClass::fetchByIdentifier(static::CONTENT_CLASS_IDENTIFIER);

        // we need the content tree node, because we need it PATH-STRING
        $mainTreeNode = \eZContentObjectTreeNode::fetch($sozialMediaFolderNode->mainNodeID());

        // create the limitations for the actions
        $limitationsList = array(
            // we limit actions to the one location; the array can contain multiple PATH-STRINGs of content tree nodes
            'Subtree' => array(
                $mainTreeNode->attribute('path_string')
            ),
            // we limit actions to the one content class; the array can contain multiple IDs of content classes
            'Class' => array(
                $contentClass->ID
            ),
        );

        // create the role
        $contentRole = \eZRole::create(static::ROLE_NAME);
        // IMPORTANT! we need to store the role before we can assign policies, otherwise we will not see any policies
        $contentRole->store();
        // add the desired policies with limitations we've just created and store AGAIN
        $contentRole->appendPolicy('content', 'create', $limitationsList);
        $contentRole->appendPolicy('content', 'read', $limitationsList);
        $contentRole->appendPolicy('content', 'remove', $limitationsList);
        $contentRole->store();

        // assign the role to a user group
        $userGroupNode = \eZContentObject::fetchByRemoteID(static::USER_GROUP_EDITORS_REMOTE_ID);
        $contentRole->assignToUser($userGroupNode->ID);
        $contentRole->store(); // and store AGAIN (mainly to clear caches for user and role ... as a side effect)

        $this->addSql(
            "SELECT 'Created Role \"" . static::ROLE_NAME . "\" and assigned to user group \"myvideo-user_group-3\"';"
        );

        //shutdown script
        $this->script->shutdown();
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_sub_homepage');
        $this->removePackage('myvideo_format_page');
        $this->removePackage('myvideo_socialmedia_comments');

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( null );

        // REMOVE ROLE /////////////////////////////////////////////////////////////////////////////////////////////////

        $contentRole = \eZRole::fetchByName(static::ROLE_NAME);
        if ($contentRole) {
            $contentRole->remove();
            $this->addSql("SELECT 'Removed Role \"" . static::ROLE_NAME . "\"';");
        } else {
            $this->addSql("SELECT 'Could not remove Role \"" . static::ROLE_NAME . "\" because it does not exist';");
        }

        // remove folders
        $success = $this->removeTreeNode($this->sozialMediaFolders['remote_id'] );

        if ( $success ) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: Folder \"%s\" with sub-folders were removed.';",
                    $this->sozialMediaFolders['attributes']['name']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Folder \"%s\" with sub-folders were not removed.';",
                    $this->sozialMediaFolders['attributes']['name']
                )
            );
        }

        $this->script->shutdown();
    }
}
