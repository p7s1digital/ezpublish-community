<?php
require_once(dirname(__FILE__) . '/common.inc.php');
$tpl = eZTemplate::factory();

$Result = array();
$Result['content'] = $tpl->fetch('design:myvideo_appendix/index.tpl');
$Result['left_menu'] = 'design:myvideo_appendix/left_menu.tpl';

$Result['path'] = helperPath(array(array('url' => false, 'text' => 'Index')));
