<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20130912093517 extends eZPublishPackageMigration
{
    const ROLE_NAME = 'Create and View MyVideo FormatPage ';
    const CONTENT_CLASS_IDENTIFIER = 'myvideo_format_page';
    const USER_GROUP_EDITORS_REMOTE_ID = 'myvideo-user_group-3';

    protected $node =
        // folder "MyVideo ImageGallery" under main-content node
        array(
            'parent_node_id' => 2,
            'class_identifier' => 'folder',
            'remote_id' => 'myvideo-content-folder-format-pages',
            'attributes' => array(
                'name' => 'MyVideo Format Seiten',
                'short_name' => 'MyVideo Format Seiten',
                'description' => 'Format Seiten für z.B. Serien',
                'short_description' => 'Format Seiten für z.B. Serien'
            )
        );

    public function up(Schema $schema)
    {
        $this->installPackage('myvideo_format_page', 'myvideo_format_page-1.0-1.ezpkg');
        $this->addSql(
            sprintf( "SELECT 'Success: MyVideo ImageGallery - initial installation';" )
        );

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance(null);

        //create and publish tree node
        $createdTreeNode = $this->createTreeNode($this->node);

        if ($createdTreeNode) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: Folder \"%s\" imported in parent_node \"%s\".';",
                    $this->node['attributes']['name'],
                    $this->node['parent_node_id']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Folder \"%s\" was not imported in parent_node \"%s\".';",
                    $this->node['attributes']['name'],
                    $this->node['parent_node_id']
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
        $contentRole->appendPolicy('content', 'edit', $limitationsList);
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
        $this->removePackage('myvideo_format_page');

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

        // REMOVE TREE NODE 'MyVideo Format Pages' ///////////////////////////////////////////////////////////////

        // remove folders
        $success = $this->removeTreeNode($this->node['remote_id']);

        if ($success) {
            $this->addSql(
                sprintf(
                    "SELECT 'Success: Folder \"%s\" with sub-folders were removed.';",
                    $this->node['attributes']['name']
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Folder \"%s\" with sub-folders were not removed.';",
                    $this->node['attributes']['name']
                )
            );
        }

        $this->script->shutdown();

    }
}
