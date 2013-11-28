<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130625145953 extends eZPublishPackageMigration
{
    protected $workflowGroupName = 'MyVideo Workflows';

    protected $workflow = array(
        'name' => 'MyVideo sync any content',
        'type' => 'group_ezserial'
    );

    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( );

        $workflowGroup = false;
        $workflow = false;

        //get $workflow group list
        $workflowGroupList = \eZWorkflowGroup::fetchList();
        foreach( $workflowGroupList AS $group ) {
            /* @var $group \eZWorkflowGroup */
            if( $group->Name == $this->workflowGroupName ) {
                $workflowGroup = $group;
                break;
            }
        }

        if( $workflowGroup ) {
            $workflowLink = \eZWorkflowGroupLink::fetchWorkflowList( 0, $workflowGroup->ID );

            foreach( $workflowLink AS $link ) {
                /* @var $link \eZWorkflowGroupLink */
                $linkedWorkflow = \eZWorkflow::fetch( $link->WorkflowID );
                if( $linkedWorkflow->attribute('name') == $this->workflow['name'] ) {
                    /* @var $workflow \eZWorkflow */
                    $workflow = $linkedWorkflow;
                    break;
                }
            }
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Cannot fetch workflow group \"%s\" for deletion.';",
                    $this->workflowGroupName
                )
            );
        }

        // get workflow id
        $workflowId = $workflow->attribute('id');

        //define and create trigger
        $triggers = array(
            array( 'function' => 'delete', 'connectType' => 'a' )
        );

        foreach ( $triggers as $trigger ) {
            $trigger['module'] = 'content';
            $triggerList = \eZTrigger::fetchList( $trigger );

            if ( count( $triggerList ) ) {
                /* @var $newTrigger \eZTrigger */
                $newTrigger = $triggerList[0];
                $newTrigger->setAttribute( 'workflow_id', $workflowId );
            } else {
                $newTrigger = \eZTrigger::createNew(
                    'content',
                    $trigger['function'],
                    $trigger['connectType'],
                    $workflowId
                );
            }
            $newTrigger->store();

            $this->addSql(
                sprintf(
                    "SELECT 'Success: Trigger \"%s %s\" was created.';",
                    $trigger['function'],
                    $trigger['module']
                )
            );
        }

        $this->script->shutdown();
    }

    public function down(Schema $schema)
    {
        $workflowGroup = false;
        $workflow = false;

        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance();

        //get $workflow group list
        $workflowGroupList = \eZWorkflowGroup::fetchList();
        foreach ($workflowGroupList AS $group) {
            /* @var $group \eZWorkflowGroup */
            if ($group->Name == $this->workflowGroupName) {
                $workflowGroup = $group;
                break;
            }
        }

        if ($workflowGroup) {
            $workflowLink = \eZWorkflowGroupLink::fetchWorkflowList(0, $workflowGroup->ID);

            foreach ($workflowLink AS $link) {
                /* @var $link \eZWorkflowGroupLink */
                $linkedWorkflow = \eZWorkflow::fetch($link->WorkflowID);
                if ($linkedWorkflow->attribute('name') == $this->workflow['name']) {
                    /* @var $workflow \eZWorkflow */
                    $workflow = $linkedWorkflow;
                    break;
                }
            }
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Cannot fetch workflow group \"%s\" for deletion.';",
                    $this->workflowGroupName
                )
            );
        }

        if ($workflow) {
            //remove triggers
            \eZTrigger::removeTriggerForWorkflow($workflow->ID);

            $this->addSql(
                sprintf(
                    "SELECT 'Success: Trigger \"%s\" was removed.';",
                    $workflow->Name
                )
            );
        } else {
            $this->addSql(
                sprintf(
                    "SELECT 'Error: Cannot fetch workflow \"%s\" for deletion.';",
                    $this->workflow['name']
                )
            );
        }

        // attention: ez can only remove all triggers
        // you have to add the old triggers again!

        //get workflow id
        $workflowId = $workflow->attribute('id');

        //define and create trigger
        $triggers = array(
            array( 'function' => 'publish', 'connectType' => 'a' ),
            array( 'function' => 'delete', 'connectType' => 'b' ),
            array( 'function' => 'hide', 'connectType' => 'a' ),
            array( 'function' => 'updateobjectstate', 'connectType' => 'a' )
        );

        foreach ($triggers as $trigger) {
            $trigger['module'] = 'content';
            $triggerList = \eZTrigger::fetchList($trigger);

            if (count($triggerList)) {
                /* @var $newTrigger \eZTrigger */
                $newTrigger = $triggerList[0];
                $newTrigger->setAttribute('workflow_id', $workflowId);
            } else {
                $newTrigger = \eZTrigger::createNew(
                    'content',
                    $trigger['function'],
                    $trigger['connectType'],
                    $workflowId
                );
            }
            $newTrigger->store();

            $this->addSql(
                sprintf(
                    "SELECT 'Success: Trigger \"%s %s\" was created.';",
                    $trigger['function'],
                    $trigger['module']
                )
            );
        }

        $this->script->shutdown();
    }
}
