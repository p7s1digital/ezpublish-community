<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cqueiser
 * Date: 29.10.13
 * Time: 13:01
 */

$OperationList = array();

/* view is the name of your module operation, can have the same name of the view */
$OperationList['sendmessage'] = array(
    'name' => 'sendmessage',
    'default_call_method' => array(
        'include_file' => 'extension/myvideo_message/modules/myvideomessage/myvideomessageoperationcollection.php',
        'class' => 'MyVideoMessageOperationCollection'),
    'parameter_type' => 'standard',
    'parameters' => array(
        array('name' => 'host',
            'type' => 'string',
            'required' => true),
        array('name' => 'object_id',
            'type' => 'integer',
            'required' => true),
        array('name' => 'version',
            'type' => 'integer',
            'required' => true)
    ),
    'keys' => array(
        'host',
        'object_id',
        'version'
    ),
    'body' => array(
        array(
            'type' => 'trigger',
            'name' => 'pre_send_message',
            'keys' => array(
                'host',
                'object_id',
                'version'
            )
        ),
        array(
            'type' => 'method',
            'name' => 'send_message',
            'frequency' => 'once',
            'method' => 'preview',
        ),
        array(
            'type' => 'trigger',
            'name' => 'post_send_message',
            'keys' => array(
                'host',
                'object_id',
                'version')
        )
    )
);
