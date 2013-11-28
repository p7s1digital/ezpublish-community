<?php

$Module = array('name' => 'MyVideo Appendix', 'variable_params' => true);

$ViewList = array();

$ViewList['index'] = array(
    'functions' => array(),
    'default_navigation_part' => 'appendixnavigationpart',
    'ui_context' => 'administration',
    'script' => 'index.php',
    'params' => array(),
    'unordered_params' => array()
);

$ViewList['redis'] = array(
    'functions' => array(),
    'ui_context' => 'administration',
    'default_navigation_part' => 'appendixnavigationpart',
    'script' => 'redis.php'
);


$ViewList['debug'] = array(
    'functions' => array(),
    'ui_context' => 'administration',
    'default_navigation_part' => 'appendixnavigationpart',
    'script' => 'debug.php',
    'unordered_params' => array(
        'offset' => 'Offset',
        'limit' => 'Limit'
    )
);

$ViewList['phpinfo'] = array(
    'functions' => array(),
    'ui_context' => 'administration',
    'default_navigation_part' => 'appendixnavigationpart',
    'script' => 'phpinfo.php'
);

$ViewList['info'] = array(
    'functions' => array(),
    'ui_context' => 'administration',
    'default_navigation_part' => 'appendixnavigationpart',
    'script' => 'info.php'
);

$ViewList['logfile'] = array(
    'functions' => array(),
    'ui_context' => 'administration',
    'default_navigation_part' => 'appendixnavigationpart',
    'script' => 'logfile.php'
);

$ViewList[''] = $ViewList['index'];

$FunctionList = array();
$FunctionList['ajax'] = array();
