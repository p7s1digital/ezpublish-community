<?php
/**
 * Video ids als eZ ContentObjecte anlegen/importieren
 *
 *
 */

const MEDIA_FOLDER_NODE_ID = 43;

$userland = array();
$userland['aid'] = $_REQUEST['aid'];
$userland['vid'] = $_REQUEST['vid'];
$userland['oid'] = $_REQUEST['oid'];

$sanitized = array_map('intval', $userland);

// Targetfolder fetch or create ...
$targetRemoteId = 'myvideo-video-import-target';
$targetObject = eZContentObject::fetchByRemoteID($targetRemoteId);

if (is_null($targetObject)) {
    $params = array();
    $params['parent_node_id'] = MEDIA_FOLDER_NODE_ID;
    $params['class_identifier'] = 'folder';
    $params['creator_id'] = 14; // admin
    $params['attributes'] = array('name' => 'Videoimport');
    $params['remote_id'] = $targetRemoteId;
    $targetObject = eZContentFunctions::createAndPublishObject($params);
}

$targetNode = $targetObject->mainNode();

$db = eZDB::instance();

$match = null;
$idlist = array();
$data = $_REQUEST['chunk'];
$success = preg_match_all('#(\d+)#ms', $data, $match);

if ($success) {
    $tmp = $match[1];
    array_map('intval', $tmp);
    $idlist = array_unique($tmp);
    $stack = array(
        'invalid' => array(),
        'new' => array(),
        'update' => array(),
        'map' => array(),
    );

    foreach ($idlist as $id):

        // until the max Range of Integer
        // see: http://www.postgresql.org/docs/9.1/static/datatype-numeric.html
        if ($id < 1 or $id > 2147483647) {
            $stack['invalid'][] = $id;
            continue;
        }

        // TODO fetch some meta data to describe the video
        // HINT default behaviour is to store WITHOUT title
        $attributesData = array('video_id' => $id);
        // import to eZ
        $params = array();
        $params['parent_node_id'] = $targetNode->NodeID;
        $params['class_identifier'] = 'myvideo_video';
        $params['creator_id'] = eZUser::currentUserID();
        $params['attributes'] = $attributesData;

        $conds = array();
        $conds['video_id'] = $id;

        $db->begin();

        // fetch objects by video_id-attribute
        $args = array(
            'ClassFilterType' => 'include',
            'ClassFilterArray' => array('myvideo_video'),
            'AttributeFilter' => array('and', array(
                'myvideo_video/video_id', '=', $id)),
            'SortBy' => array(array('modified', eZContentObjectTreeNode::SORT_ORDER_DESC))
        );

        $nodeList = eZContentObjectTreeNode::subTreeByNodeID($args, MEDIA_FOLDER_NODE_ID);

        $contentObject = !empty($nodeList) ? array_shift($nodeList)->object() : null;

        if (is_null($contentObject)) {
            $contentObject = eZContentFunctions::createAndPublishObject($params);
            $stack['new'][] = $contentObject->ID;
            $stack['valid'][] = $contentObject->ID;
        } else {
            eZContentFunctions::updateAndPublishObject($contentObject, $params);
            $stack['update'][] = $contentObject->ID;
            $stack['valid'][] = $contentObject->ID;
        }

        $dataMap = $contentObject->dataMap();
        $subTitle = $dataMap['subtitle']->content();

        $stack['map'][$contentObject->ID] = array('title' => $contentObject->Name, 'subtitle' => $subTitle);
        $db->commit();

    endforeach;
}
$data = array('ids' => $stack);

$obj = new MyVideoUpdateObjectRelations($sanitized['oid'], $sanitized['vid']);
$obj->addContentObjectIds($stack['valid']);

$data['debug'] = $sanitized;

header('Content-Type:text/json');
print json_encode($data);

eZExecution::cleanExit();
