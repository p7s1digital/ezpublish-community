<?php
/**
 * Render an eZPublish contentType myvideo_api_text
 */

class MyVideoRenderMyVideoApiText extends MyVideoRendererBase implements MyVideoRendererInterface
{

    /**
     * this is a config content type
     */
    const KEY_PART_TYPE = 'api';

    /**
     * use a literal string as identifier part for redis key generation
     * their shoulb be only one config object
     */
    const KEY_PART_IDENTIFIER = '/text';

    /**
     * use a literal string as identifier part for redis key generation
     * @return string
     */
    protected function fetchKeyPartIdentifier()
    {
        return self::KEY_PART_IDENTIFIER . "/" . $this->getAttributeValue('id');
    }

    /**
     * render simplyfied version
     *
     */
    public function render()
    {
        $standard = parent::render();
        $data = array();
        $data['type'] = $standard['type'];

        // TODO use helper method
        $data['id'] = $standard['fields']['id']['content'];
        $data['name'] = $standard['fields']['name']['content'];
        $data['title'] = $standard['fields']['title']['content'];
        $data['body'] = $standard['fields']['text']['content'];
        return $data;
    }
}
