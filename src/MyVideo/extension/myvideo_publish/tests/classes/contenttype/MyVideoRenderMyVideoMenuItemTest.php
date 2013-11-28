<?php
/**
 * Description of MyVideoRenderMyVideoMenuItemTest
 */

class MyVideoRenderMyVideoMenuItemTest extends MyVideoContentTypeTestCase
{
    const CONTENT_TYPE_IDENTIFIER = 'myvideo_menu_item';

    /**
     * check by identifier if the content type does exists
     * @group myvideo
     */
    function testContentTypeExists()
    {
        $contentClass = eZContentClass::fetchByIdentifier(self::CONTENT_TYPE_IDENTIFIER);
        $this->assertInstanceOf('eZContentClass', $contentClass);
    }


    /**
     * create a sample object and validate renderer response against json schema
     * @group myvideo
     */
    function testRenderResultAgainstSchema()
    {
        // create sample object ...
        $params = array();
        $params['parent_node_id'] = 43; // MEDIA
        $params['class_identifier'] = self::CONTENT_TYPE_IDENTIFIER;
        $params['creator_id'] = 14; // admin
        $params['attributes'] = array('title' => 'Test', 'url' => 'Testurl', 'frontend_id' => '7893');

        $contentObject = $this->helperCreateObject($params);

        $renderer = MyVideoRenderFactory::getInstance($contentObject);
        $key = $renderer->fetchKey();

        $storage = mxdRedis::getInstance();
        $dataFromStorage = $storage->get($key);
        $this->assertNull($dataFromStorage, self::CONTENT_TYPE_IDENTIFIER . ' are NOT stored to redis, so we expect a null value');

        $dataFromRenderer = $renderer->render();
        $this->assertInternalType('array', $dataFromRenderer, self::CONTENT_TYPE_IDENTIFIER . ' should return an array');

        $this->helperValidateJSONAgainstSchema(json_encode($dataFromRenderer));
    }

}
