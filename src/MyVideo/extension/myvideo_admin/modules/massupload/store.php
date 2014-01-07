<?php
$userId = ezUser::currentUserID();
$languageId = eZContentObject::defaultLanguage();
$languageId = eZContentLanguage::idByLocale($languageId);
$imageGalleryId = +$_REQUEST['id'];

$contentVersion = eZContentObjectVersion::fetchLatestUserDraft($imageGalleryId, $userId, $languageId);
$versionNumber = $contentVersion->attribute('version');

$imageGalleryObject = $contentVersion->contentObject();
$galleryDatamap = $imageGalleryObject->fetchDataMap($versionNumber);
$imageGalleryAttribute = $galleryDatamap['image_list'];

$tpl = eZTemplate::factory();
$tpl->setVariable('attribute', $imageGalleryAttribute);
$content = $tpl->fetch('design:massupload/myvideo_imageTeaserlist-relation.tpl');

echo $content;

eZExecution::cleanExit();
