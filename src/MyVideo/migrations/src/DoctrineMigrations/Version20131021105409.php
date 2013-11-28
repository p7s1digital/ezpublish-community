<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Migration for Voting Campaigns
 */
class Version20131021105409 extends eZPublishPackageMigration
{
    const ROLE_NAME = 'Create and View MyVideo Voting Campains';
    const CONTENT_CLASS_IDENTIFIER = 'myvideo_voting_campaign';
    const USER_GROUP_EDITORS_REMOTE_ID = 'myvideo-user_group-3';

    // folder "MyVideo Voting" under main-content node
    private $folders = array(
        'parent_node_id' => 2,
        'class_identifier' => 'folder',
        'remote_id' => 'myvideo-folder-voting-campain',
        'attributes' => array(
            'name' => 'MyVideo Voting',
            'short_name' => 'MyVideo Voting',
            'description' => 'In diesem Ordner befinden sich alle Voting Kampagnen für Gewinnspiele auf MyVideo.',
            'short_description' => 'Voting Kampagnen für Gewinnspiele auf MyVideo.'
        )
    );


    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_voting_campaign', 'myvideo_voting_campaign-1.0-1.ezpkg');
        $this->addSql(
            sprintf("SELECT 'Success: MyVideo Voting Campaign - Added new Content Class';")
        );

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        $createdTreeNode = $this->createTreeNode($this->folders);

        if ($createdTreeNode) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: Folder \"%s\" imported in parent_node \"%s\".';",
                    $this->folders['attributes']['name'],
                    $this->folders['parent_node_id']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Folder \"%s\" was not imported in parent_node \"%s\".';",
                    $this->folders['attributes']['name'],
                    $this->folders['parent_node_id']
                )
            );
        }

        // CREATE ROLE /////////////////////////////////////////////////////////////////////////////////////////////////

        // we need the content class, because we need its ID
        $contentClass = \eZContentClass::fetchByIdentifier(static::CONTENT_CLASS_IDENTIFIER);
        // we need the content tree node, because we need it PATH-STRING
        $mainTreeNode = \eZContentObjectTreeNode::fetch($createdTreeNode->mainNodeID());

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
        $contentRole->appendPolicy('voting', '*');
        $contentRole->store();

        // assign the role to a user group
        $userGroupNode = \eZContentObject::fetchByRemoteID(static::USER_GROUP_EDITORS_REMOTE_ID);
        $contentRole->assignToUser($userGroupNode->ID);
        $contentRole->store(); // and store AGAIN (mainly to clear caches for user and role ... as a side effect)

        $this->addSql(
            "SELECT 'Created Role \"" . static::ROLE_NAME . "\" and assigned to user group \"myvideo-user_group-3\"';"
        );

        $this->script->shutdown();
    }

    public function down(Schema $schema)
    {
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
        $success = $this->removeTreeNode($this->folders['remote_id'] );

        if ( $success ) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: Folder \"%s\" with sub-folders were removed.';",
                    $this->folders['attributes']['name']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Folder \"%s\" with sub-folders were not removed.';",
                    $this->folders['attributes']['name']
                )
            );
        }

        $this->script->shutdown();

        $this->removePackage('myvideo_voting_campaign');

        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo Voting Campaign - Content Class Removed;'" )
        );

    }
}
