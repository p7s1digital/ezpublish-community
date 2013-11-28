<?php
/*
 * Removes the preview redis key after calling this file.
 * @param int objectId // this parameter has to given via request
 */

header('Content-Type:text/json');

$objectId = isset($_REQUEST['objectId']) ? (int)$_REQUEST['objectId'] : false;
$error = array();

if ($objectId !== 0) {
    $contentObject = eZContentObject::fetch($objectId);
    if ($contentObject instanceof eZContentObject) {
        MyVideoRenderer::render($contentObject, MyVideoRenderer::VISIBILITY_MODE_DISCARD_PREVIEW);
        MyVideoCacheManager::expire($contentObject);
    } else {
        $error[] = 'Not a valid object id given!';
    }
} else {
    $error[] = 'no object id given!';
}

$result = array();
if (!empty($error)) {
    $result['error'] = $error;
}

print json_encode($result);

eZExecution::cleanExit();
