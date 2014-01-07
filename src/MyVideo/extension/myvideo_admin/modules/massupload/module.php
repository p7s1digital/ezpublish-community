<?php
$Module = array('name' => 'MyVideo mass upload');

$ViewList = array();

$ViewList['admin'] = array(
    'functions' => array(),
    'default_navigation_part' => 'massuploadpart',
    'script' => 'index.php',
);

$ViewList['store'] = array(
    'functions' => array(),
    'default_navigation_part' => 'massuploadstorepart',
    'script' => 'store.php'
);

$ViewList[''] = $ViewList['admin'];

$FunctionList = array();
