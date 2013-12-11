<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Bugfix Roles to edit Player Page
 */
class Version20131209143111 extends eZPublishPackageMigration
{
    const ROLE_NAME = 'Create and View MyVideo Player Page';
    const CONTENT_CLASS_IDENTIFIER = 'myvideo_player_page';
    const USER_GROUP_EDITORS_REMOTE_ID = 'myvideo-user_group-3';
    const FOLTER_IDENTIFIER = 'myvideo-content-folder-playerpage';


    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        // CREATE ROLE /////////////////////////////////////////////////////////////////////////////////////////////////

        // we need the content class, because we need its ID
        $contentClass = \eZContentClass::fetchByIdentifier(static::CONTENT_CLASS_IDENTIFIER);

        $mainTreeNode = $this->getTreeNode(self::FOLTER_IDENTIFIER);

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
        $contentRole = \eZRole::fetchByName(static::ROLE_NAME);

        // add the desired policies with limitations we've just created and store AGAIN
        $contentRole->appendPolicy('content', 'edit', $limitationsList);
        $contentRole->store();

        // assign the role to a user group
        $userGroupNode = \eZContentObject::fetchByRemoteID(static::USER_GROUP_EDITORS_REMOTE_ID);
        $contentRole->assignToUser($userGroupNode->ID);
        $contentRole->store(); // and store AGAIN (mainly to clear caches for user and role ... as a side effect)

        $this->addSql("SELECT 'Added Role Rule Edit for Player Page';");

        //shutdown script
        $this->script->shutdown();
    }

    public function down(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        $contentRole = \eZRole::fetchByName(static::ROLE_NAME);
        $contentRole->removePolicy('content', 'edit');
        $contentRole->store();

        $this->addSql("SELECT 'Removed Role Rule Edit for Player Page';");

        //shutdown script
        $this->script->shutdown();
    }
}
