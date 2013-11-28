<?php

/**
 * Class MyVideoPreviewType
 * Handles workflow events.
 * For each trigger type you have to implement a corresponding method in this class
 */
class MyVideoPreviewType extends MyVideoGenericEventType
{
    const WORKFLOW_TYPE_STRING = 'myvideopreview';

    public function __construct()
    {
        parent::__construct(self::WORKFLOW_TYPE_STRING, 'MyVideo Preview');
        $this->workflow = self::WORKFLOW_TYPE_STRING;
        $this->logFile = 'redis.log';
        $this->setTriggerTypes(array('myvideopublish' => array('preview' => array('after'))));
        $GLOBALS['MyVideoPreviewTypeAsGlobal'] = $this;

    }

    public function renderPreviewContentObject()
    {
        mxdLog::write(__METHOD__);

        /** @var eZContentObjectVersion $contentObjectVersion */
        $contentObjectVersion = eZContentObjectVersion::fetchVersion($this->workflowProcessParameters['version'], $this->workflowProcessParameters['object_id']);
        $this->contentObject = eZContentObject::fetch($this->workflowProcessParameters['object_id']);
        eZContentObject::clearCache($this->workflowProcessParameters['object_id']);
        $this->contentObject->resetDataMap();
        $attributes = $contentObjectVersion->contentObjectAttributes();
        $version = $this->workflowProcessParameters['version'];
        $language = $this->contentObject->CurrentLanguage;
        $this->contentObject->setContentObjectAttributes($attributes, $version, $language);
        $this->renderAndExpire(MyVideoRenderer::VISIBILITY_MODE_PREVIEW);
    }

    public function triggerPostPreview()
    {
    }

    /**
     * Start the rendering and cache expire process
     *
     * @param int $visibilityMode
     * @return int
     */
    private function renderAndExpire($visibilityMode = MyVideoRenderer::VISIBILITY_MODE_NONE)
    {
        MyVideoRenderer::render($this->contentObject, $visibilityMode);
        MyVideoCacheManager::expire($this->contentObject);

        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType(MyVideoPreviewType::WORKFLOW_TYPE_STRING, 'MyVideoPreviewType');
