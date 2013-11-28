<?php

require_once(dirname(__FILE__) . '/common.inc.php');

$flagTableOnly = false;

ob_start();
phpinfo();
$content = ob_get_contents();
ob_end_clean();

if ($flagTableOnly) {
    $content = preg_replace('#^.*<body>#ms', '', $content);
    $content = preg_replace('#</body>.*$#ms', '', $content);
}
$tpl = eZTemplate::factory();
$tpl->setVariable('content', $content);

$Result = array();
$Result['content'] = $tpl->fetch('design:myvideo_appendix/phpinfo.tpl');
$Result['left_menu'] = 'design:myvideo_appendix/left_menu.tpl';

$Result['path'] = helperPath(array(array('url' => false, 'text' => 'Index')));
