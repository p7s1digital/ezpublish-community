<?php

$FunctionList = array();

// Get the JSON from the redis
$FunctionList['json'] = array(
    'name' => 'MyVideoPublish JSON',
    'operation_types' => array('read'),
    'call_method' => array('include_file' => 'extension/myvideo_publish/modules/myvideopublish/myvideopublishfunctioncollection.php',
        'class' => 'MyVideoPublishFunctionCollection',
        'method' => 'fetchJSON'),
    'parameter_type' => 'standard',
    'parameters' => array(
        array('name' => 'node_id',
            'type' => 'string',
            'required' => false,
            'default' => null,
        ),
        array('name' => 'language',
            'type' => 'string',
            'required' => false,
            'default' => null,
        )

    ));

// Rendering the json on the fly
$FunctionList['render'] = array(
    'name' => 'MyVideoPublish Render On The Fly',
    'operation_types' => array('read'),
    'call_method' => array('include_file' => 'extension/myvideo_publish/modules/myvideopublish/myvideopublishfunctioncollection.php',
        'class' => 'MyVideoPublishFunctionCollection',
        'method' => 'fetchRenderedJSON'),
    'parameter_type' => 'standard',
    'parameters' => array(
        array('name' => 'node_id',
            'type' => 'string',
            'required' => false,
            'default' => null,
        ),
        array('name' => 'language',
            'type' => 'string',
            'required' => false,
            'default' => null,
        )
    ));
