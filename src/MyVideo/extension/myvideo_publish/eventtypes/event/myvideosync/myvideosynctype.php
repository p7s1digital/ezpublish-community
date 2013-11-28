<?php

/**
 * Class MyVideoSyncType
 * Handles workflow events.
 * For each trigger type you have to implement a corresponding method in this class
 */
class MyVideoSyncType extends MyVideoGenericEventType
{
    const WORKFLOW_TYPE_STRING = 'myvideosync';

    /** @var array */
    public static $preContent = array();

    /** @var MyVideoCacheManager */
    private $cacheManager;

    public function __construct()
    {
        parent::__construct(MyVideoSyncType::WORKFLOW_TYPE_STRING, 'MyVideo Sync Redis Storage');
        $this->workflow = self::WORKFLOW_TYPE_STRING;
        $this->logFile = 'redis.log';
    }

    /**
     * hide a _node_ so dependent objects have to be rendered as well
     * This trigger fires when the visibility of a node change
     */
    public function triggerPostHide()
    {
        /** @var eZContentObjectTreeNode $theNode */
        $theNode = eZContentObjectTreeNode::fetch($this->workflowProcessParameters['node_id']);
        $this->contentObject = $theNode->object();
        $this->renderAndExpire();
    }

    /**
     * Start the rendering and cache expire process
     *
     * @param int $visibilityMode
     * @return int
     */
    private function renderAndExpire($visibilityMode = MyVideoRenderer::VISIBILITY_MODE_NONE)
    {
        $preContent = null;
        if (is_object($this->contentObject) && isset(static::$preContent[$this->contentObject->ID])) {
            $preContent = static::$preContent[$this->contentObject->ID];
        }
        MyVideoRenderer::render($this->contentObject, $visibilityMode, $preContent);
        MyVideoCacheManager::expire($this->contentObject);

        return eZWorkflowType::STATUS_ACCEPTED;
    }

    public function triggerPrePublish()
    {
        eZContentObject::clearCache($this->workflowProcessParameters['object_id']);

        /** @var eZContentObjectVersion $version */
        $version = eZContentObjectVersion::fetchVersion(
            $this->workflowProcessParameters['version'],
            $this->workflowProcessParameters['object_id'],
            true
        );

        $contentObject = $version->contentObject();

        if ($contentObject instanceof eZContentObject) {
            static::$preContent[$contentObject->ID] = MyVideoRenderer::renderData($contentObject);
        }
    }

    /**
     * Publish an _object_ so dependent objects have to be rendered as well
     * This trigger fires when the object is published
     */
    public function triggerPostPublish()
    {
        $this->contentObject = eZContentObject::fetch($this->workflowProcessParameters['object_id']);
        $this->renderAndExpire(MyVideoRenderer::VISIBILITY_MODE_LIVE);
    }

    public function triggerPostUpdateobjectstate()
    {
        $this->contentObject = eZContentObject::fetch($this->workflowProcessParameters['object_id']);
        $this->renderAndExpire(MyVideoRenderer::VISIBILITY_MODE_LIVE);
    }

    /**
     * This trigger fires when the object will be delete.
     * In ez publish you can't fetch the nodes/object after delete, so we have to store them
     */
    public function triggerPreDelete()
    {
        foreach ($this->workflowProcessParameters['node_id_list'] as $nodeID) {
            $theNode = eZContentObjectTreeNode::fetch($nodeID);
            static::$preContent[$nodeID] = $theNode;
            $contentObject = $theNode->object();
            $this->cacheManager = new MyVideoCacheManager();
            $this->cacheManager->setTriggeredObject($contentObject);
            $this->cacheManager->addToQueue($contentObject);

            while ($this->cacheManager->hasPendingObjectsInQueue()) {
                $this->cacheManager->processPendingObject();
            }
        }
    }

    /**
     * delete the stored nodes out of the redis
     */
    public function triggerPostDelete()
    {
        foreach ($this->workflowProcessParameters['node_id_list'] as $nodeID) {
            $theNode = static::$preContent[$nodeID];

            if ($theNode instanceof eZContentObjectTreeNode) {
                MyVideoRenderer::deleteRedisData($theNode);
            }
        }

        foreach ($this->cacheManager->getItsExpireList() as $contentObject) {
            if ($contentObject == $this->cacheManager->getTriggeredObject()) {
                continue;
            }
            MyVideoRenderer::render($contentObject, MyVideoRenderer::VISIBILITY_MODE_LIVE);
        }

    }
}

eZWorkflowEventType::registerEventType(MyVideoSyncType::WORKFLOW_TYPE_STRING, 'myvideosynctype');
