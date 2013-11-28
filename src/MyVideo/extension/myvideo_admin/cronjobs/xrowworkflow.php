<?php

xrowworkflowLog::write('Running extension/myvideo_admin/cronjobs/xrowworkflow.php');

//login as admin
$user = eZUser::fetch(eZINI::instance()->variable('UserSettings', 'UserCreatorID'));
$user->loginCurrent();

eZINI::instance()->setVariable('SiteAccessSettings', 'ShowHiddenNodes', 'false');
$nodeID = 1;
$rootNode = eZContentObjectTreeNode::fetch($nodeID);
/* can`t take multiple extended filters
array(
    'id' => 'ObjectStateFilter' ,
    'params' => array(
        'states_identifiers' => array(
            xrowworkflow::STATE_GROUP . '/' . xrowworkflow::ONLINE ,
            xrowworkflow::STATE_GROUP . '/' . xrowworkflow::QUEUE
        ) ,
        'operator' => 'or'
    )
) ,
*/


if (!$isQuiet) {
    $cli->output('Expire content of node "' . $rootNode->attribute('name') . '" (' . $nodeID . ')');
    $cli->output();
}

$params = array(
    'Limitation' => array(),
    'ExtendedAttributeFilter' => array(
        'id' => 'xrowworkflow_expire',
        'params' => array()
    )
);

$nodeArrayCount = $rootNode->subTreeCount($params);

if ($nodeArrayCount > 0) {
    if (!$isQuiet) {
        $cli->output("Hiding {$nodeArrayCount} node(s).");
    }
    $params['Limit'] = 500;
    do {
        $nodeArray = $rootNode->subTree($params);
        foreach ($nodeArray as $node) {
            $workflow = xrowworkflow::fetchByContentObjectID($node->ContentObjectID);
            if ($workflow instanceof xrowworkflow) {
                $workflow->offline();
            }
            if (!$isQuiet) {
                xrowworkflowLog::write('Hiding node: ' . $node->attribute('name') . '(' . $node->attribute('node_id') . ')');
                $cli->output('Hiding node: "' . $node->attribute('name') . '" (' . $node->attribute('node_id') . ')');
            }
        }
        eZContentObject::clearCache();
    } while (is_array($nodeArray) and count($nodeArray) > 0);
}


if (!$isQuiet) {
    $cli->output('Publishing content of node "' . $rootNode->attribute('name') . '" (' . $nodeID . ')');
    $cli->output();
}

$params = array(
    'Limitation' => array(),
    'ExtendedAttributeFilter' => array(
        'id' => 'xrowworkflow_publish',
        'params' => array()
    )
);
$nodeArrayCount = $rootNode->subTreeCount($params);

if ($nodeArrayCount > 0) {
    if (!$isQuiet) {
        $cli->output("Publishing {$nodeArrayCount} node(s).");
    }
    $params['Limit'] = 500;
    do {
        $nodeArray = $rootNode->subTree($params);
        foreach ($nodeArray as $node) {
            $workflow = xrowworkflow::fetchByContentObjectID($node->ContentObjectID);
            if ($workflow instanceof xrowworkflow) {
                $workflow->online();
            }

            if (!$isQuiet) {
                xrowworkflowLog::write('Publishing node: ' . $node->attribute('name') . '(' . $node->attribute('node_id') . ')');
                $cli->output('Publishing node: "' . $node->attribute('name') . '" (' . $node->attribute('node_id') . ')');
            }
        }
        eZContentObject::clearCache();
    } while (is_array($nodeArray) and count($nodeArray) > 0);
}
xrowworkflowLog::write('Completing extension/myvideo_admin/cronjobs/xrowworkflow.php');
