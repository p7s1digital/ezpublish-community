<?php
/**
 * Description of MyVideoRenderMyVideoVideoTest
 */

class MyVideoRenderMyVideoVideoTest extends MyVideoContentTypeTestCase
{
    const CONTENT_TYPE_IDENTIFIER = 'myvideo_video';

    /**
     * @group myvideo
     */
    function testContentTypeExists()
    {
        $contentClass = eZContentClass::fetchByIdentifier(self::CONTENT_TYPE_IDENTIFIER);
        $this->assertInstanceOf('eZContentClass', $contentClass);
    }

    /**
     * @group myvideo
     */
    function testRenderResultAgainstSchemaMinimumAttributes()
    {
        // create video object
        $params = array();
        $params['parent_node_id'] = 43;
        $params['class_identifier'] = self::CONTENT_TYPE_IDENTIFIER;
        $params['creator_id'] = 14; // admin
        $params['attributes'] = array('video_id' => '7893');

        $contentObject = $this->helperCreateObject($params);

        $renderer = MyVideoRenderFactory::getInstance($contentObject);
        $key = $renderer->fetchKey();

        $storage = mxdRedis::getInstance();
        $dataFromStorage = $storage->get($key);
        $this->assertNull($dataFromStorage, 'video items are NOT stored to redis, so we expect a null value');

        $dataFromRenderer = $renderer->render();
        $this->assertInternalType('array', $dataFromRenderer, 'video items should return an array');


        $this->helperValidateJSONAgainstSchema(json_encode($dataFromRenderer));
    }

    /**
     * @group myvideo
     */
    function testRenderResultAgainstSchemaMaximumAttributes()
    {
        // create video object
        $params = array();
        $params['parent_node_id'] = 43;
        $params['class_identifier'] = self::CONTENT_TYPE_IDENTIFIER;
        $params['creator_id'] = 14; // admin
        $params['attributes'] = array(
            'video_id' => '7894',
            'title' => 'Hallo Welt',
            'subtitle' => 'Der Untertitel',
        );


        $contentObject = $this->helperCreateObject($params);

        $renderer = MyVideoRenderFactory::getInstance($contentObject);
        $key = $renderer->fetchKey();

        $storage = mxdRedis::getInstance();
        $dataFromStorage = $storage->get($key);
        $this->assertNull($dataFromStorage, 'video items are NOT stored to redis, so we expect a null value');

        $dataFromRenderer = $renderer->render();
        $this->assertInternalType('array', $dataFromRenderer, 'video items should return an array');


        $this->helperValidateJSONAgainstSchema(json_encode($dataFromRenderer));
    }


}
