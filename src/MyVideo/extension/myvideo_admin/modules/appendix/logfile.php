<?php
/**
 * simple view to check some logfiles in the cms
 */

// some commonly used stuff ...
require_once(dirname(__FILE__) . '/common.inc.php');

// how many lines to get from each logfile ...
$numberOfLines = 50;

// which logfiles to check ...
$logfiles = array(
    'redis.log' => eZSys::rootDir() . '/var/log/redis.log',
    'myvideo_cdn_storage.log' => eZSys::rootDir() . '/var/log/myvideo_cdn_storage.log',
    'error.log' => eZSys::rootDir() . '/var/log/error.log',
    'debug.log' => eZSys::rootDir() . '/var/log/debug.log',
    'warning.log' => eZSys::rootDir() . '/var/log/warning.log',
);

// iterate and store result in buffer ...
$data = array();

foreach ($logfiles as $logfile => $logfile_path) {
    $buffer = array();
    if (!file_exists($logfile_path)) {
        $buffer[] = 'Can not read file ' . $logfile_path;
    }
    exec("tail -n $numberOfLines $logfile_path", $buffer, $ret);
    $data[$logfile] = join("\n", $buffer);
}

// instantiate template and populate data ...
$tpl = eZTemplate::factory();
$vars = array('content' => $data);
$tpl->setVariable('vars', $vars);

// build module result array ...
$Result = array();
$Result['content'] = $tpl->fetch('design:myvideo_appendix/logfile.tpl');
$Result['left_menu'] = 'design:myvideo_appendix/left_menu.tpl';
$Result['path'] = helperPath(array(array('url' => false, 'text' => 'Logfiles')));
