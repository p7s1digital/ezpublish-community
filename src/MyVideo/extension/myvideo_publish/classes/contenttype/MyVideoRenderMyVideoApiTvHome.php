<?php
/**
 * Render an eZPublish contentType myvideo_api_text
 */

class MyVideoRenderMyVideoApiTvHome extends MyVideoRendererBase implements MyVideoRendererInterface
{

    /**
     * this is a api content type
     */
    const KEY_PART_TYPE = 'api';

    /**
     * use a literal string as identifier part for redis key generation
     * their shoulb be only one config object
     */
    const KEY_PART_IDENTIFIER = '/home';

    /**
     * path to powered by logo
     */
    const PBY_LOGO = '/ez/ott/unscaled/';

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

        //tv column
        $tv = $this->renderItem($standard['fields']['tv']['content']['relation_list']);
        if (!empty($tv)) {
            $data['video_objects']['TV'] = $tv;
        }

        //music column
        $data['video_objects']['MUSIK'] = $this->renderItem($standard['fields']['musik']['content']['relation_list']);

        //movies column
        $data['video_objects']['FILME'] = $this->renderItem($standard['fields']['filme']['content']['relation_list']);

        //get cdn host from config
        $ini = \eZINI::instance('environment.ini');
        $host = 'http://' . $ini->variable('RiakSettings', 'CDNHost');

        //absolute url to powered by logo
        $thumbnail = "";
        if (isset($standard['fields']['logo_url']['content']['thumbnail'])) {
            $thumbnail = $standard['fields']['logo_url']['content']['thumbnail'];
        }

        $data['logo_url'] = (!empty($thumbnail)) ? $host . self::PBY_LOGO . $thumbnail : "";

        return $data;
    }

    private function renderItem($data)
    {
        $buffer = array();

        foreach ($data as $item) {
            $itemNode = eZContentObjectTreeNode::fetch($item['node_id']);

            if (is_object($itemNode)) {
                if ($itemNode->IsInvisible || !$this->isContentObjectOnline($itemNode->ContentObject)) {
                    continue;
                }

                $renderer = MyVideoRenderFactory::getInstance($itemNode->ContentObject);
                $buffer[] = $renderer->render();
            }
        }

        return $buffer;
    }
}
