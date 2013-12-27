<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131211150948 extends eZPublishPackageMigration
{
    const ROLE_NAME = 'Create and View MyVideo Highlight Page';
    const CONTENT_CLASS_IDENTIFIER = 'myvideo_highlight_page';
    const USER_GROUP_EDITORS_REMOTE_ID = 'myvideo-user_group-3';

    //parent folder configuration
    protected $folder = array(
        'parent_node_id' => 2,
        'class_identifier' => 'folder',
        'remote_id' => 'myvideo-content-folder-highlight_page',
        'attributes' => array (
            'name' => 'MyVideo Highlight Pages',
            'short_name' => 'MyVideo Highlight Pages',
            'description' => 'MyVideo Highlight Pages',
            'short_description' => 'MyVideo Highlight Pages'
        )
    );

    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_highlight_page', 'myvideo_highlight_page-1.0-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Highlight Page Created';")
        );

        //create parent folder
        $folderNode = $this->createTreeNode($this->folder);

        if ($folderNode) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: Folder \"%s\" imported in parent_node \"%s\".';",
                    $this->folder['attributes']['name'],
                    $this->folder['parent_node_id']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Folder \"%s\" was not imported in parent_node \"%s\".';",
                    $this->folder['attributes']['name'],
                    $this->folder['parent_node_id']
                )
            );
        }

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        // CREATE ROLE /////////////////////////////////////////////////////////////////////////////////////////////////

        // we need the content class, because we need its ID
        $contentClass = \eZContentClass::fetchByIdentifier(static::CONTENT_CLASS_IDENTIFIER);

        // we need the content tree node, because we need it PATH-STRING
        $mainTreeNode = \eZContentObjectTreeNode::fetch($folderNode->mainNodeID());

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
            "SELECT 'Created Role \"" . static::ROLE_NAME . "\" and assigned to user group \"" . self::USER_GROUP_EDITORS_REMOTE_ID . "\"';"
        );

        //shutdown script
        $this->script->shutdown();
    }

    public function down(Schema $schema)
    {
        $this->removePackage('myvideo_highlight_page');

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        // REMOVE ROLE /////////////////////////////////////////////////////////////////////////////////////////////////

        $contentRole = \eZRole::fetchByName(static::ROLE_NAME);
        if ($contentRole) {
            $contentRole->remove();
            $this->addSql("SELECT 'Removed Role \"" . static::ROLE_NAME . "\"';");
        } else {
            $this->addSql("SELECT 'Could not remove Role \"" . static::ROLE_NAME . "\" because it does not exist';");
        }

        // remove folders
        $success = $this->removeTreeNode($this->folder['remote_id']);

        if ($success) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: Folder \"%s\" with sub-folders were removed.';",
                    $this->folder['attributes']['name']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Folder \"%s\" with sub-folders were not removed.';",
                    $this->folder['attributes']['name']
                )
            );
        }

        $this->script->shutdown();
    }
}
