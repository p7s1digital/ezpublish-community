<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130305160001 extends eZPublishPackageMigration
{
    protected $workflowGroupName = 'MyVideo Workflows';

    protected $workflow = array(
        'name' => 'MyVideo sync any content',
        'type' => 'group_ezserial'
    );

    protected $event = array(
        'placement' => 1,
        'type' => 'event_mxdpublishsync',
        'description' => 'Beschreibung, nach jedem Publizieren, Löschen etc. muss das redis storage aktualisiert werden.'
    );


    /**
     * upgrade ez-db with import of workflow with group, event and trigger
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance( );

        //admin user
        $userId = 14;

        //create and store workflow group
        $workflowGroup = \eZWorkflowGroup::create( $userId );
        $workflowGroup->setAttribute( 'name', $this->workflowGroupName );
        $workflowGroup->store();

        $this->addSql(
            sprintf(
                "SELECT 'Success: Workflow group \"%s\" was created.';",
                $this->workflowGroupName
            )
        );

        //create workflow
        $workflow = \eZWorkflow::create( $userId );
        $workflow->setAttribute( 'name', $this->workflow['name'] );
        $workflow->setAttribute( 'workflow_type_string',  $this->workflow['type'] );
        $workflow->store();

        $this->addSql(
            sprintf(
                "SELECT 'Success: Workflow \"%s\" was created.';",
                $this->workflow['name']
            )
        );

        // get workflow id
        $workflowId = $workflow->attribute( 'id' );

        //add workflow to workflow group
        $ingroup = \eZWorkflowGroupLink::create(
            $workflow->attribute( 'id' ),
            $workflow->attribute( 'version' ),
            $workflowGroup->attribute( 'id' ),
            $this->workflowGroupName
        );
        $ingroup->store();

        // get eventlist for defined workflow
        $eventList = $workflow->fetchEvents();

        //create event
        $event = \eZWorkflowEvent::create( $workflowId, $this->event['type'] );

        /* @var $eventType \eZWorkflowType */
        $eventType = $event->eventType();

        $workflow->store( $eventList );

        $eventType->initializeEvent( $event );

        $event->setAttribute( 'placement', $this->event['placement'] );
        $event->setAttribute( 'description', $this->event['description'] );
        $event->store();
        $eventList[] = $event;

        $workflow->store( $eventList );

        //cleanup workflow group links
        // Remove old version 0 first
        \eZWorkflowGroupLink::removeWorkflowMembers( $workflowId, 0 );

        $linkedGroups = \eZWorkflowGroupLink::fetchGroupList( $workflowId, 1 );
        foreach( $linkedGroups as $linkedGroup )
        {
            /* @var $linkedGroup \eZWorkflowGroupLink */
            $linkedGroup->setAttribute("workflow_version", 0 );
            $linkedGroup->store();
        }
        // Remove workflow members version 1
        \eZWorkflowGroupLink::removeWorkflowMembers( $workflowId, 1 );
        // Remove events
        \eZWorkflow::removeEvents( false, $workflowId, 0 );
        $workflow->removeThis( true );
        $workflow->setVersion( 0, $eventList );
        $workflow->adjustEventPlacements( $eventList );
        $workflow->storeDefined( $eventList );
        $workflow->cleanupWorkFlowProcess();
        $workflow->store();

        $this->addSql(
            sprintf(
                "SELECT 'Success: Workflow Event \"%s\" was created.';",
                $this->event['type']
            )
        );

        //define and create trigger
        $triggers = array(
            array( 'function' => 'publish', 'connectType' => 'a' ),
            array( 'function' => 'delete', 'connectType' => 'b' ),
            array( 'function' => 'hide', 'connectType' => 'a' )
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

    /**
     * downgrade ez-db with removal of workflows
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
        $workflowGroup = false;
        $keepGroup = false;
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
            // don't delete group if there are more workflows defined in workflow-group
            if( count($workflowLink) > 1) {
                $keepGroup = true;
            }

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

        if( $workflow ) {
            // get all events
            $events = $workflow->fetchEvents();
            //remove triggers
            \eZTrigger::removeTriggerForWorkflow( $workflow->ID );
            // remove process
            $workflow->cleanupWorkFlowProcess();
            //remove workflow events
            \eZWorkflow::removeEvents( $events );

            $this->addSql(
                sprintf(
                    "SELECT 'Success: All triggers and events for workflow \"%s\" were removed.';",
                    $workflow->Name
                )
            );

            //remove group workflow link
            \eZWorkflowGroupLink::removeByID( $workflow->ID, 0, $workflowGroup->ID );
            //delete workflow and events
            \eZWorkflow::removeWorkflow( $workflow->ID, 0 );

            $this->addSql(
                sprintf(
                    "SELECT 'Success: Workflow \"%s\" was removed.';",
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

        if( !$keepGroup ) {
            //delete workflow group
            \eZWorkflowGroup::removeSelected($workflowGroup->ID);

            $this->addSql(
                sprintf(
                    "SELECT 'Success: Workflow group \"%s\" was removed.';",
                    $this->workflowGroupName
                )
            );
        }
    }
}
