<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131202135813 extends eZPublishPackageMigration
{
    const ROLE_NAME = 'Create and View MyVideo Static Pages';
    const CONTENT_CLASS_IDENTIFIER = 'myvideo_staticpage';
    const USER_GROUP_EDITORS_REMOTE_ID = 'myvideo-user_group-3';

    //parent folder configuration
    protected $staticPageFolder = array(
        'parent_node_id' => 2,
        'class_identifier' => 'folder',
        'remote_id' => 'myvideo-staticpage',
        'attributes' => array (
            'name' => 'MyVideo Static Pages',
            'short_name' => 'MyVideo Static Pages',
            'description' => 'MyVideo Static Pages',
            'short_description' => 'MyVideo Static Pages'
        )
    );

    public function up(Schema $schema)
    {
        $this->installPackage(self::CONTENT_CLASS_IDENTIFIER, 'myvideo_staticpage-1.0-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Static Page - Added new class for static content';")
        );

        //create parent folder
        $staticPageNode = $this->createTreeNode($this->staticPageFolder);

        if ($staticPageNode) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: Folder \"%s\" imported in parent_node \"%s\".';",
                    $this->staticPageFolder['attributes']['name'],
                    $this->staticPageFolder['parent_node_id']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Folder \"%s\" was not imported in parent_node \"%s\".';",
                    $this->staticPageFolder['attributes']['name'],
                    $this->staticPageFolder['parent_node_id']
                )
            );
        }

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        // CREATE ROLE /////////////////////////////////////////////////////////////////////////////////////////////////

        // we need the content class, because we need its ID
        $contentClass = \eZContentClass::fetchByIdentifier(static::CONTENT_CLASS_IDENTIFIER);

        // we need the content tree node, because we need it PATH-STRING
        $mainTreeNode = \eZContentObjectTreeNode::fetch($staticPageNode->mainNodeID());

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
        $contentRole->appendPolicy('content', 'edit', $limitationsList);
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
        $this->removePackage('myvideo_staticpage');

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
        $success = $this->removeTreeNode($this->staticPageFolder['remote_id'] );

        if ( $success ) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: Folder \"%s\" with sub-folders were removed.';",
                    $this->staticPageFolder['attributes']['name']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Folder \"%s\" with sub-folders were not removed.';",
                    $this->staticPageFolder['attributes']['name']
                )
            );
        }

        $this->script->shutdown();
    }
}
