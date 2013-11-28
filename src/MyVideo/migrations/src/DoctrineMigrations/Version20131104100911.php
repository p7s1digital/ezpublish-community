<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131104100911 extends eZPublishPackageMigration
{
    protected $workflowGroupName = 'MyVideo Workflows';

    protected $workflow = array(
        'name' => 'MyVideo Send Message',
        'type' => 'group_ezserial'
    );

    protected $event = array(
        'placement' => 1,
        'type' => 'event_myvideosendmessage',
        // event-description may not exceed 50! characters in length
        'description' => 'Beschreibung, nach jedem Publizieren, Löschen etc'
    );

    protected $workflowSyncRedis = array(
        'name' => 'MyVideo sync any content'
    );

    public function up(Schema $schema)
    {
        // Initialize the eZ Script Interface.
        $this->script = $this->getEzScriptInstance();

        //admin user
        $userId = 14;

        $workflowGroup = null;

        //get $workflow group list
        $workflowGroupList = \eZWorkflowGroup::fetchList();
        foreach ($workflowGroupList AS $group) {
            /* @var $group \eZWorkflowGroup */
            if ($group->Name == $this->workflowGroupName) {
                $workflowGroup = $group;
                break;
            }
        }

        //create workflow
        $workflow = \eZWorkflow::create($userId);
        $workflow->setAttribute('name', $this->workflow['name']);
        $workflow->setAttribute('workflow_type_string', $this->workflow['type']);
        $workflow->store();

        $this->addSql(
            sprintf(
                "SELECT 'Success: Workflow \"%s\" was created.';",
                $this->workflow['name']
            )
        );

        // get workflow id
        $workflowId = $workflow->attribute('id');

        //add workflow to workflow group
        $ingroup = \eZWorkflowGroupLink::create(
            $workflow->attribute('id'),
            $workflow->attribute('version'),
            $workflowGroup->attribute('id'),
            $this->workflowGroupName
        );
        $ingroup->store();

        // get eventlist for defined workflow
        $eventList = $workflow->fetchEvents();

        //create event
        $event = \eZWorkflowEvent::create($workflowId, $this->event['type']);

        /* @var $eventType \eZWorkflowType */
        $eventType = $event->eventType();

        $workflow->store($eventList);

        $eventType->initializeEvent($event);

        $event->setAttribute('placement', $this->event['placement']);
        $event->setAttribute('description', $this->event['description']);
        $event->store();
        $eventList[] = $event;

        $workflow->store($eventList);


        //cleanup workflow group links
        // Remove old version 0 first
        \eZWorkflowGroupLink::removeWorkflowMembers($workflowId, 0);

        $linkedGroups = \eZWorkflowGroupLink::fetchGroupList($workflowId, 1);
        foreach ($linkedGroups as $linkedGroup) {
            /* @var $linkedGroup \eZWorkflowGroupLink */
            $linkedGroup->setAttribute("workflow_version", 0);
            $linkedGroup->store();
        }

        // Remove workflow members version 1
        \eZWorkflowGroupLink::removeWorkflowMembers($workflowId, 1);
        // Remove events
        \eZWorkflow::removeEvents(false, $workflowId, 0);
        $workflow->removeThis(true);
        $workflow->setVersion(0, $eventList);
        $workflow->adjustEventPlacements($eventList);
        $workflow->storeDefined($eventList);
        $workflow->cleanupWorkFlowProcess();
        $workflow->store();

        $this->addSql(
            sprintf(
                "SELECT 'Success: Workflow Event \"%s\" was created.';",
                $this->event['type']
            )
        );

        ################ START Add an event to an existing Workflow ################
        /** @var \eZWorkflow $existingWorkflow */
        $existingWorkflow = null;
        $workflowLink = \eZWorkflowGroupLink::fetchWorkflowList(0, $workflowGroup->ID);
        foreach ($workflowLink AS $link) {
            /* @var $link \eZWorkflowGroupLink */
            $linkedWorkflow = \eZWorkflow::fetch($link->WorkflowID);
            if ($linkedWorkflow->attribute('name') == $this->workflowSyncRedis['name']) {
                /* @var $existingWorkflow \eZWorkflow */
                $existingWorkflow = $linkedWorkflow;
                break;
            }
        }
        $this->workflowSyncRedis['id'] = $existingWorkflow->attribute('id');

        // get eventlist for defined workflow
        $existingEventList = $existingWorkflow->fetchEvents();

        //create event
        $extraEvent = \eZWorkflowEvent::create($this->workflowSyncRedis['id'], $this->event['type']);

        /* @var $eventType \eZWorkflowType */
        $eventType = $extraEvent->eventType();
        $eventType->initializeEvent($extraEvent);
        $extraEvent->setAttribute('placement', $this->event['placement']);
        $extraEvent->setAttribute('description', $this->event['description']);
        $extraEvent->store();
        $existingEventList[] = $extraEvent;

        $existingWorkflow->store($existingEventList);

        // Remove old event and sets the new events
        \eZWorkflow::removeEvents(false, $this->workflowSyncRedis['id'], 0);
        $existingWorkflow->removeThis(true);
        $existingWorkflow->setVersion(0, $existingEventList);
        $existingWorkflow->adjustEventPlacements($existingEventList);
        $existingWorkflow->storeDefined($existingEventList);
        $existingWorkflow->cleanupWorkFlowProcess();
        $existingWorkflow->store();

        $this->addSql(
            sprintf(
                "SELECT 'Success: Workflow Event \"%s\" was created in existing Workflow \"%s\".';",
                $this->event['type'], $this->workflowSyncRedis['name']
            )
        );
        ################ END Add an event to an existing Workflow ################

        $this->script->shutdown();
    }

    public function down(Schema $schema)
    {
        $workflowGroup = false;
        $keepGroup = false;
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
            // don't delete group if there are more workflows defined in workflow-group
            if (count($workflowLink) > 1) {
                $keepGroup = true;
            }

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
            // get all events
            $events = $workflow->fetchEvents();
            //remove triggers
            \eZTrigger::removeTriggerForWorkflow($workflow->ID);
            // remove process
            $workflow->cleanupWorkFlowProcess();
            //remove workflow events
            \eZWorkflow::removeEvents($events);

            $this->addSql(
                sprintf(
                    "SELECT 'Success: All triggers and events for workflow \"%s\" were removed.';",
                    $workflow->Name
                )
            );

            //remove group workflow link
            \eZWorkflowGroupLink::removeByID($workflow->ID, 0, $workflowGroup->ID);
            //delete workflow and events
            \eZWorkflow::removeWorkflow($workflow->ID, 0);

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

        if (!$keepGroup) {
            //delete workflow group
            \eZWorkflowGroup::removeSelected($workflowGroup->ID);

            $this->addSql(
                sprintf(
                    "SELECT 'Success: Workflow group \"%s\" was removed.';",
                    $this->workflowGroupName
                )
            );
        }

        ################ START Remove extra event ################
        /** @var \eZWorkflow $existingWorkflow */
        $existingWorkflow = null;
        $workflowLink = \eZWorkflowGroupLink::fetchWorkflowList(0, $workflowGroup->ID);
        foreach ($workflowLink AS $link) {
            /* @var $link \eZWorkflowGroupLink */
            $linkedWorkflow = \eZWorkflow::fetch($link->WorkflowID);
            if ($linkedWorkflow->attribute('name') == $this->workflowSyncRedis['name']) {
                /* @var $existingWorkflow \eZWorkflow */
                $existingWorkflow = $linkedWorkflow;
                break;
            }
        }
        $this->workflowSyncRedis['id'] = $existingWorkflow->attribute('id');

        // get eventlist for defined workflow
        /* @var $existingWorkflow \eZWorkflow */
        $existingEventList = $existingWorkflow->fetchEvents();

        foreach ($existingEventList as $key => $event) {
            if ($event->TypeString === $this->event['type']) {
                unset($existingEventList[$key]);
            }
        }

        $existingWorkflow->store($existingEventList);

        // Remove old event and sets the new events
        \eZWorkflow::removeEvents(false, $this->workflowSyncRedis['id'], 0);
        $existingWorkflow->removeThis(true);
        $existingWorkflow->setVersion(0, $existingEventList);
        $existingWorkflow->adjustEventPlacements($existingEventList);
        $existingWorkflow->storeDefined($existingEventList);
        $existingWorkflow->cleanupWorkFlowProcess();
        $existingWorkflow->store();

        $this->addSql(
            sprintf(
                "SELECT 'Success: Extra Workflow Event \"%s\" was removed.';",
                $this->event['type'], $this->workflowSyncRedis['name']
            )
        );

        ################ END Remove extra event ################

        $this->script->shutdown();
    }
}
