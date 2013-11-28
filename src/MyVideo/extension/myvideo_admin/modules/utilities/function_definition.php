<?php

$FunctionList = array();

$FunctionList['by_object_id'] = array('name' => 'by_object_id',
    'call_method' => array(
        'class' => 'xrowWorkflowFunctionCollection',
        'method' => 'byObjectID'),
    'parameter_type' => 'standard',
    'parameters' => array(array('name' => 'id',
        'type' => 'integer',
        'required' => true)));

$FunctionList['myvideotracking_by_object_id'] = array('name' => 'myvideotracking_by_object_id',
    'call_method' => array(
        'class' => 'myvideoTrackingFunctionCollection',
        'method' => 'byObjectID'),
    'parameter_type' => 'standard',
    'parameters' => array(array('name' => 'id',
        'type' => 'integer',
        'required' => true)));

$FunctionList['myvideometadata_by_object_id'] = array('name' => 'myvideometadata_by_object_id',
    'call_method' => array(
        'class' => 'myvideoMetaDataFunctionCollection',
        'method' => 'byObjectID'),
    'parameter_type' => 'standard',
    'parameters' => array(array('name' => 'id',
        'type' => 'integer',
        'required' => true)));

$FunctionList['myvideosimadconfig_by_object_id'] = array('name' => 'myvideosimadconfig_by_object_id',
    'call_method' => array(
        'class' => 'myvideoSimAdConfigFunctionCollection',
        'method' => 'byObjectID'),
    'parameter_type' => 'standard',
    'parameters' => array(array('name' => 'id',
        'type' => 'integer',
        'required' => true)));
