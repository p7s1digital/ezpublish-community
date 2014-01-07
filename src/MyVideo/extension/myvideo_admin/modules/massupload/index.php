<?php
$contentclassAttributeID = 604;
$parentNoteId = 453;
$result = array('errors' => array());
$upload = new eZContentUpload();
$upload->handleUpload($result, 'file', $parentNoteId, false);
$imageObject = $result['contentobject'];

$title = '"' . $imageObject->Name . '" vom ' . date('d.m.Y H:i') . ' Uhr';
$params = array();
$params['parent_node_id'] = $parentNoteId;
$params['class_identifier'] = 'myvideo_image_teaser';
$params['myvideo_image_teaser_title'] = $title;
$params['creator_id'] = ezUser::currentUserID();
$params['attributes'] = array('title' => $title);


$imageTeaserObject = eZContentFunctions::createAndPublishObject($params);
$imageDatamap = $imageObject->dataMap();
$imageAttribute = $imageDatamap['image'];
$stringImageAttribute = $imageAttribute->toString();
$imageTeaserObjectDatamap = $imageTeaserObject->dataMap();
$imageTeaserObjectAttribute = $imageTeaserObjectDatamap['image'];
$imageTeaserObjectAttribute->fromString($stringImageAttribute);
$imageTeaserObjectAttribute->store();

$contentVersion = eZContentObjectVersion::fetchLatestUserDraft(
    $_REQUEST['id'],
    ezUser::currentUserID(),
    eZContentLanguage::idByLocale(eZContentObject::defaultLanguage())
);

$imageGalleryObject = $contentVersion->contentObject();
$versionNumber = $contentVersion->attribute('version');
$galleryDatamap = $imageGalleryObject->fetchDataMap($versionNumber);
if (isset($galleryDatamap['image_list'])) {
    $imageGalleryAttribute = $galleryDatamap['image_list'];
    $content = $imageGalleryAttribute->content();
}

if (!isset($content['relation_list'])) {
    $imageGalleryAttribute = eZContentObjectAttribute::create(
        $contentclassAttributeID,
        $imageGalleryObject->ID
    );
    $content = $imageGalleryAttribute->content();
}
$priority = count($content['relation_list']) + 1;
$eZObjectRelationListType = new eZObjectRelationListType();
$content['relation_list'][] = $eZObjectRelationListType->appendObject(
    $imageTeaserObject->attribute('id'),
    $priority,
    $imageGalleryAttribute
);
$imageGalleryAttribute->setContent($content);
$imageGalleryAttribute->store();

eZContentObjectOperations::remove($imageObject->attribute('id'));
eZContentObject::clearCache($imageGalleryObject->attribute('id'));
$imageGalleryObject->store();

echo $imageGalleryObject->attribute('current_version');
eZExecution::cleanExit();


