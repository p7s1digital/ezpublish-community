<?php
/**
 * a view to show some redis state information in admin
 */
require_once(dirname(__FILE__) . '/common.inc.php');

$tpl = eZTemplate::factory();

try {

    $redis = mxdRedis::getInstance();
    $arr = $redis->info();

} catch (Exception $e) {

    $arr = array('Exception' => array('Message' => $e->getMessage()));
}

$tpl->setVariable('result', $arr);

$Result = array();
$Result['content'] = $tpl->fetch('design:myvideo_appendix/redis.tpl');
$Result['left_menu'] = 'design:myvideo_appendix/left_menu.tpl';

$Result['path'] = helperPath(array(array('url' => false, 'text' => 'Redis Info')));
