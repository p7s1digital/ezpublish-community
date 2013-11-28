<?php
/**
 * Render an eZ Publish content object to json and store into redis
 */
class MyVideoRenderer
{
    const FAULT_MISSING_NODE_FOR_LANGUAGE = 'FAULT-MISSING-NODE-FOR-LANGUAGE';

    const KEY_PREFIX_TAGPAGE = 'Themen';
    const KEY_PREFIX_FOOTER = 'Footer';

    const VISIBILITY_MODE_NONE = 0;
    const VISIBILITY_MODE_LIVE = 1;
    const VISIBILITY_MODE_PREVIEW = 2;
    const VISIBILITY_MODE_DISCARD_PREVIEW = 3;
    private static $visibiltyMode = self::VISIBILITY_MODE_NONE;

    /**
     * Render an eZ Publish content object if is visible and
     * check whether it could be stored in the redis
     *
     * @param eZContentObject $contentObject
     * @param int $vMode
     * @param null $prePublishData
     * @throws Exception
     * @return bool
     */
    public static function render(eZContentObject $contentObject, $vMode = self::VISIBILITY_MODE_NONE, $prePublishData = null)
    {
        if (!$contentObject instanceof eZContentObject) {
            throw new Exception('Renderer called with invalid eZ content object.');
        }

        if ($vMode !== self::VISIBILITY_MODE_NONE) {
            self::$visibiltyMode = $vMode;
        }

        if (self::getRedisSettingsOfContentObject($contentObject) === false) {
            return false;
        }

        // an object can be stored in more content tree locations
        $nodeList = $contentObject->assignedNodes();
        foreach ($nodeList as $aNode) {
            self::renderAllLanguages($aNode, $prePublishData);
        }

        return true;
    }

    /**
     * get the Redis settings of the content object and check whether it is allowed to store a key in redis
     *
     * @param $contentObject
     * @return bool
     */
    public static function getRedisSettingsOfContentObject($contentObject)
    {
        $ini = eZINI::instance('redis.ini');
        $classList = $ini->variable('ContentTypes', 'ClassList');
        $flagFilterEnabled = $ini->variable('ContentTypes', 'ClassFilter') != 'disabled';

        $allowedStoreInRedis = self::allowedStoreInRedis($contentObject, $classList, $flagFilterEnabled);

        if ($allowedStoreInRedis === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * check if this content type should be stored in redis
     *
     * @param eZContentObject $contentObject
     * @param $classList
     * @param $flagFilterEnabled
     * @return bool
     */
    private static function allowedStoreInRedis($contentObject, $classList, $flagFilterEnabled)
    {
        if ($flagFilterEnabled && !in_array($contentObject->attribute('class_identifier'), $classList)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param eZContentObject $obj
     * @return array Contains the rendered data for each language ['de' => ..., 'en' => ...]
     */
    public static function renderData(eZContentObject $obj)
    {
        $ret = array();

        $renderer = MyVideoRenderFactory::getInstance($obj);

        foreach ($obj->availableLanguages() as $language) {
            $renderer->setLanguage($language);

            $ret[$language] = $renderer->render();
        }

        return $ret;
    }

    /**
     * Delete the given content node in the redis key
     *
     * @param eZContentObjectTreeNode $node
     * @return bool
     */
    public static function deleteRedisData(eZContentObjectTreeNode $node)
    {
        $contentObject = $node->object();
        $renderer = MyVideoRenderFactory::getInstance($contentObject);
        $renderer->setLanguage($node->CurrentLanguage);
        $renderer->handleDelete($node);

        if (self::getRedisSettingsOfContentObject($contentObject) !== false) {
            $renderer->deletePrimaryRedisData();
            $sendMessage = new MyVideoSendMessageType();
            $sendMessage->sendMessage(MyVideoSendMessageType::DELETE_REQUEST, $sendMessage->getExtendedContentObjectInfo($node));
        }
        return true;
    }

    /**
     * Loop over all languages versions of the content object
     *
     * @param eZContentObjectTreeNode $node
     * @param array $prePublishData
     */
    private static function renderAllLanguages(eZContentObjectTreeNode $node, $prePublishData)
    {
        $contentObject = $node->object();
        $renderer = MyVideoRenderFactory::getInstance($contentObject);
        $stateID = eZContentObjectState::fetchByIdentifier(xrowworkflow::ONLINE, eZContentObjectStateGroup::fetchByIdentifier(xrowworkflow::STATE_GROUP)->ID)->ID;

        $flagIsVisible = !$node->IsInvisible;
        $flagIsOnline = in_array($stateID, $contentObject->attribute('state_id_array'));

        // TODO: there is a bug in here!
        // Imagine that a content object is attached to multiple locations in the content tree. Now when we iterate
        // over all the nodes (see render() above) then the last node wins. So if the last node is hidden, a key will
        // be removed from redis. This is wrong if the content element is still visible somewhere else in the tree.

        foreach ($contentObject->availableLanguages() as $language) {
            $renderer->setLanguage($language);
            $key = $renderer->fetchKey();
            $sendMessage = new MyVideoSendMessageType();
            $messageType = null;

            // store only when object is visible
            if ($flagIsVisible && $flagIsOnline) {
                // do additional update logic
                $renderer->handleUpdate($prePublishData);
                // update key
                $data = $renderer->render();
                $json = json_encode($data);
                // store the primary data
                $renderer->storePrimaryRedisData($json);
                if (self::isLiveMode()) {
                    $messageType = MyVideoSendMessageType::PUBLISH_REQUEST;
                } elseif (self::isPreviewMode()) {
                    $messageType = MyVideoSendMessageType::PREVIEW_REQUEST;
                }
            } else {
                // do additional delete logic
                $renderer->handleDelete($node);
                // delete the primary data
                $renderer->deletePrimaryRedisData();
                $messageType = MyVideoSendMessageType::DELETE_REQUEST;
            }

            if (self::isLiveMode() || self::isDiscardPreviewMode()) {
                $storage = new MyVideoStorageRedis();
                $previewKey = str_replace('live', 'preview', $key);
                $storage->delete($previewKey);
            }
            $sendMessage->sendMessage($messageType, $sendMessage->getExtendedContentObjectInfo($node));
        }
    }

    public static function isLiveMode()
    {
        return self::$visibiltyMode == self::VISIBILITY_MODE_LIVE;
    }

    public static function isPreviewMode()
    {
        return self::$visibiltyMode == self::VISIBILITY_MODE_PREVIEW;
    }

    public static function isDiscardPreviewMode()
    {
        return self::$visibiltyMode == self::VISIBILITY_MODE_DISCARD_PREVIEW;
    }
}
