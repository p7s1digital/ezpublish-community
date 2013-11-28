<?php
/**
 * Render an eZPublish contentType myvideo_config
 */

class MyVideoRenderMyVideoConfig extends MyVideoRendererBase implements MyVideoRendererInterface
{

    /**
     * this is a config content type
     */
    #const KEY_PART_TYPE = 'config';

    /**
     * use a literal string as identifier part for redis key generation
     * their shoulb be only one config object
     */
    const KEY_PART_IDENTIFIER = 'myvideo-config';

    /**
     * use a literal string as identifier part for redis key generation
     * @return string
     */
    protected function fetchKeyPartIdentifier()
    {
        return self::KEY_PREFIX . self::KEY_PART_IDENTIFIER;
    }

    /**
     * Renders the homepage object again to and place the config data into the specific groups. Then store into redis.
     * We need to place this here so that the config data are stored into the homepage object when changing the configs.
     * @see: MyVideoRenderMyVideoHomepage
     */
    public function render()
    {
        $filterList = array(
            'ClassFilterType' => 'include',
            'Depth', 10,
            'ClassFilterArray' => array('myvideo_homepage')
        );

        $subTreeNodeId = eZINI::instance('content.ini')->variable('NodeSettings', 'RootNode');
        $nodeList = eZContentObjectTreeNode::subTreeByNodeID($filterList, $subTreeNodeId);

        $contentObject = $nodeList[0]->ContentObject;

        MyVideoRenderer::render($contentObject, MyVideoRenderer::VISIBILITY_MODE_LIVE);
        MyVideoCacheManager::expire($contentObject);
    }


    /**
     * Get all the attributes from the myvideo_config object and build an well formed array to group the
     * attributes into different sections
     *
     * the prefix until the first _ should be exactly the same key like the redis live key sections.
     *
     * e.g.
     * "stage_rotation_delay" will result in
     * array('stage' => array('rotationDelay' => [value]))
     *
     * @return array
     */
    public function getConfigData()
    {

        $data = array();
        $this->flushCachedRenderResult();
        $dataMap = $this->getAllAttributeValues();

        foreach ($dataMap['fields'] as $field) {
            $slicedFieldName = explode('_', $field['name']);
            $groupName = $slicedFieldName[0];
            $data[$groupName] = isset($data[$groupName]) ? $data[$groupName] : array();

            // remove the group string from field name and add camel case format
            $formedFieldName = MyVideoUtils::toCamelCase(str_replace($groupName . '_', '', $field['name']));
            $data[$groupName][$formedFieldName] = $this->getAttributeValue($field['name']);
        }

        return $data;
    }
}
