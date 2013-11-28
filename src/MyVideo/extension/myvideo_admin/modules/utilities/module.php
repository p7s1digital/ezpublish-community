<?php
$Module = array('name' => 'MyVideo Utilities', 'variable_params' => true);
$ViewList = array();
$ViewList['edit-and-redirect-afterwards'] = array(
    'functions' => array(),
    'ui_context' => 'administration',
    'default_navigation_part' => 'appendixnavigationpart',
    'script' => 'edit-and-redirect-afterwards.php',
);

$ViewList['store'] = array(
    'functions' => array(),
    'default_navigation_part' => 'appendixnavigationpart',
    'ui_context' => 'administration',
    'script' => 'store.php',
    'params' => array(),
    'unordered_params' => array()
);

$ViewList['discard_redis_preview'] = array(
    'functions' => array(),
    'ui_context' => 'administration',
    'script' => 'discard_redis_preview.php',
    'params' => array(),
    'unordered_params' => array()
);

// user roles to access this module
// empty = all access
$FunctionList = array();
