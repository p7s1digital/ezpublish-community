<?php
require_once(dirname(__FILE__) . '/common.inc.php');

$viewParameters = array();
// check offset
$viewParameters = array_merge($viewParameters, array('offset' => (isset($Params['Offset']) and is_numeric($Params['Offset'])) ? $Params['Offset'] : 0));
// check limit
$viewParameters = array_merge($viewParameters, array('limit' => (isset($Params['Limit']) and is_numeric($Params['Limit'])) ? $Params['Limit'] : 25));

$tpl = eZTemplate::factory();
$tpl->setVariable('view_parameters', $viewParameters);

$Result = array();
$Result['content'] = $tpl->fetch('design:myvideo_appendix/debug.tpl');
$Result['left_menu'] = 'design:myvideo_appendix/left_menu.tpl';

$Result['path'] = helperPath(array(array('url' => false, 'text' => 'Index')));
