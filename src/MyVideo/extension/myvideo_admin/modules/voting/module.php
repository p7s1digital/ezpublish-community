<?php
$Module = array('name' => 'MyVideo Voting');

$ViewList = array();

$ViewList['campaign'] = array(
    'functions' => array('read'),
    'default_navigation_part' => 'votingnavigationpart',
    'script' => 'index.php',
    'params' => array('campaignId')
);
$ViewList['admin'] = array(
    'functions' => array('read'),
    'default_navigation_part' => 'votingnavigationpart',
    'script' => 'index.php',
);

$ViewList[''] = $ViewList['campaign'];

// user roles to access this module
// empty = all access
$FunctionList = array();
$FunctionList['read'] = array();
