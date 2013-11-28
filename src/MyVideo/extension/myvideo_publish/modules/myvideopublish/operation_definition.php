<?php

$OperationList = array();

/* view is the name of your module operation, can have the same name of the view */
$OperationList['preview'] = array(
    'name' => 'preview',
    'default_call_method' => array(
        'include_file' => 'extension/myvideo_publish/modules/myvideopublish/myvideopublishoperationcollection.php',
        'class' => 'MyVideoPublishOperationCollection'),
    'parameter_type' => 'standard',
    'parameters' => array(
        array('name' => 'object_id',
            'type' => 'integer',
            'required' => true),
        array('name' => 'version',
            'type' => 'integer',
            'required' => true)
    ),
    'keys' => array(
        'object_id',
        'version'
    ),
    'body' => array(
        array(
            'type' => 'trigger',
            'name' => 'pre_preview',
            'keys' => array(
                'object_id',
                'version'
            )
        ),
        array(
            'type' => 'method',
            'name' => 'preview',
            'frequency' => 'once',
            'method' => 'preview',
        ),
        array(
            'type' => 'trigger',
            'name' => 'post_preview',
            'keys' => array(
                'object_id',
                'version')
        )
    )
);
