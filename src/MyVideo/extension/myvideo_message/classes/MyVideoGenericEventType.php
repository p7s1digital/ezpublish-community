<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cqueiser
 * Date: 01.11.13
 * Time: 13:34
 * To change this template use File | Settings | File Templates.
 */

class MyVideoGenericEventType extends eZWorkflowEventType
{
    /** @var eZWorkflowProcess $workflowProcessParameters */
    public $workflowProcessParameters = null;
    /** @var eZContentObject $contentObject */
    public $contentObject = null;
    /** @var eZWorkflowProcess $workflowProcess */
    private $workflowProcess = null;
    /** @var eZWorkflowEvent $workflowEvent */
    private $workflowEvent = null;

    protected $workflow = '';
    protected $logFile = '';

    /**
     * Executed by every trigger call
     *
     * @param $process eZWorkflowProcess
     * @param $event eZWorkflowEvent
     * @return int|mixed
     */
    public function execute($process, $event)
    {
        $this->workflowProcess = $process;
        $this->workflowEvent = $event;
        $this->workflowProcessParameters = $this->workflowProcess->attribute('parameter_list');
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        eZLog::write('Entering ' . $this->workflow . ' workflow' . ' at ' . @$trace[1]['file'] . ':' . @$trace[1]['line'], $this->logFile, 'var/log');
        $method = $this->getTriggerMethod();

        if ($method) {
            eZLog::write('call trigger method ' . $this->getTriggerMethodName() . ' at ' . @$trace[1]['file'] . ':' . @$trace[1]['line'], $this->logFile, 'var/log');
            return call_user_func(array($this, $method));
        } else {
            eZLog::write('FAIL missing method ' . $this->getTriggerMethodName() . ' at ' . @$trace[1]['file'] . ':' . @$trace[1]['line'], $this->logFile, 'var/log');
            return ezWorkflowType::STATUS_ACCEPTED;
        }
    }

    /**
     * Returns the trigger method
     *
     * @return bool|string
     */
    private function getTriggerMethod()
    {
        $methodName = $this->getTriggerMethodName();
        $result = method_exists($this, $methodName) ? $methodName : false;

        return $result;
    }

    /**
     * Returns the name of the currently called trigger method
     *
     * @return string
     */
    private function getTriggerMethodName()
    {
        $trigger = $this->getTriggerName();
        $tmp = str_replace('_', ' ', $trigger);
        $tmp = ucwords($tmp);
        $methodName = 'trigger' . str_replace(' ', '', $tmp);

        return $methodName;
    }

    /**
     * Returns the name of the currently called trigger
     *
     * @return String
     */
    private function getTriggerName()
    {
        $params = unserialize($this->workflowProcess->Parameters);
        return $params['trigger_name'];
    }
}
