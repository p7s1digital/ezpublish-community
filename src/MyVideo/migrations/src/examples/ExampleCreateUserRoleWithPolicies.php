<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Example migration that demonstrates how to create a user roll.
 *
 * What it does:
 * - create the user role
 * - assign policies
 *      -> which actions can be done...
 *      -> where in the content tree can they be done
 *      -> on which content classes can they be done
 * - assign role to users or a user group
 */
class ExampleCreateUserRoleWithPolicies extends eZPublishPackageMigration
{
    /** The display name of the role */
    const ROLE_NAME = 'Example Role Name';
    /** Identifier of a content class that will be used in the policies for the role */
    const CONTENT_CLASS_IDENTIFIER = 'myvideo_example_content_class';
    /** RemoteId of the content tree node, that will be used in the policies  */
    const CONTENT_TREE_NODE_REMOTE_ID = 'some_folder_remote_id';
    /** RemoteId of the user group, that we will asign the role to  */
    const USER_GROUP_REMOTE_ID = 'some_user_group_remote_id';

    /**
     * upgrade ez-db with navigation folder structure
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        // CREATE LIMITATIONS //////////////////////////////////////////////////////////////////////////////////////////

        // we need the content class, because we need its ID
        $contentClass = \eZContentClass::fetchByIdentifier(static::CONTENT_CLASS_IDENTIFIER);
        // we need the content tree node, because we need it PATH-STRING
        $mainTreeNode = \eZContentObjectTreeNode::fetchByRemoteID(static::CONTENT_TREE_NODE_REMOTE_ID);

        // create the limitations for the actions
        $limitationsList = array(
            // we limit actions to the one location; the array can contain multiple PATH-STRINGs of content tree nodes
            // HINT: it seems like it is better to first define "Subtree" and the "Class", because it did not seem to
            //       work the other way around. Not 100% sure about that.
            'Subtree' => array(
                $mainTreeNode->attribute('path_string')
            ),
            // we limit actions to the one content class; the array can contain multiple IDs of content classes
            'Class' => array(
                $contentClass->ID
            ),
        );

        // CREATE ROLE /////////////////////////////////////////////////////////////////////////////////////////////////

        // create the role
        $contentRole = \eZRole::create(static::ROLE_NAME);
        // IMPORTANT! we need to store the role before we can assign policies, otherwise we will not see any policies
        $contentRole->store();
        // add the desired policies with limitations we've just created and store AGAIN
        $contentRole->appendPolicy('content', 'create', $limitationsList);
        $contentRole->appendPolicy('content', 'read', $limitationsList);
        $contentRole->store();

        // ASSIGN ROLE TO USER GROUP ///////////////////////////////////////////////////////////////////////////////////

        $userGroupNode = \eZContentObject::fetchByRemoteID(static::USER_GROUP_REMOTE_ID);
        $contentRole->assignToUser($userGroupNode->ID);
        $contentRole->store(); // and store AGAIN (mainly to clear caches for user and role ... as a side effect)

        // SOME OUTPUT ///////////// ///////////////////////////////////////////////////////////////////////////////////

        $this->addSql(
            "SELECT 'Created Role \"" . static::ROLE_NAME . "\" and assigned to user group \"" .
            static::USER_GROUP_REMOTE_ID ."\"';"
        );

        //shutdown script
        $this->script->shutdown();
    }

    /**
     * downgrade ez-db with removal of navigation folder structure
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
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

        $this->script->shutdown();
    }
}
