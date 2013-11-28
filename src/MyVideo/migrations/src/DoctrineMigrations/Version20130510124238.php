<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130510124238 extends eZPublishPackageMigration
{

    const WORKFLOWNAME = 'MyVideo sync any content';

    protected $group = array(
        'name' => 'publishstate',
        'desc' => 'Publish in Redis',
        'states' => array(
            'live',
            'expired',
            'pending'
        )
    );

    protected $trigger = array(
        'module' => 'content',
        'function' => 'updateobjectstate',
        'connectType' => 'a'
    );


    public function up(Schema $schema)
    {
        $script = $this->getEzScriptInstance();

        $stateGroup = \eZContentObjectStateGroup::fetchByIdentifier($this->group['name']);

        if(!$stateGroup) {

            // create stategroup
            $stateGroup = new \eZContentObjectStateGroup( array('identifier' => $this->group['name']));

            $languages = \eZContentLanguage::prioritizedLanguages();

            foreach($languages AS $language) {
                $localeToUse = $language->attribute( 'locale' );

                $trans = $stateGroup->translationByLocale( $localeToUse );
                $trans->setAttribute( 'name', ucfirst($this->group['name']) );
                $trans->setAttribute( 'description', $this->group['desc'] );
            }

            $stateGroup->store();

            // create states
            foreach($this->group['states'] AS $stateId) {
                $state = $stateGroup->newState($stateId);
                foreach($languages AS $language) {
                    $localeToUse = $language->attribute( 'locale' );

                    $trans = $state->translationByLocale( $localeToUse );
                    $trans->setAttribute( 'name', ucfirst($stateId) . " State" );
                    $trans->setAttribute( 'description', ucfirst($stateId) . " State" );
                }
                $state->store();
            }

            //get workflow id
            $workflowList = \eZWorkflow::fetchList();
            foreach($workflowList AS $workflow) {
                if($workflow->Name == self::WORKFLOWNAME) {
                    $workflowId = $workflow->ID;
                }
            }

            //create new linked trigger
            \eZTrigger::createNew( $this->trigger['module'], $this->trigger['function'], $this->trigger['connectType'], $workflowId );

            $this->addSql(
                sprintf( "SELECT 'Success: Added Stategroups, Triggers and Events for xworkflow;'" )
            );

        } else {
            $this->addSql(
                sprintf( "SELECT 'Warning: Stategroup already exists;'" )
            );
        }

        $script->shutdown();
    }

    public function down(Schema $schema)
    {
        $script = $this->getEzScriptInstance();

        //remove group and group states
        $group = \eZContentObjectStateGroup::fetchByIdentifier($this->group['name']);

        if($group) {

            $stateIds = array();
            foreach($this->group['states'] AS $stateIdentifier) {
                $state = $group->stateByIdentifier($stateIdentifier);
                $stateIds[] = $state->ID;
            }
            $group->removeStatesByID($stateIds);
            $group->remove();

            //remove trigger
            $triggerList = \eZTrigger::fetchList($this->trigger);
            foreach($triggerList AS $trigger) {
                $trigger->remove();
            }

            $this->addSql(
                sprintf( "SELECT 'Success: Removed Stategroups, Triggers and Events for xworkflow;'" )
            );
        }

        $script->shutdown();
    }
}
