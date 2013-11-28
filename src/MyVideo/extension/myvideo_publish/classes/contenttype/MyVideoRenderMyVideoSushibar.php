<?php
/**
 * Description of MyVideoRenderMyVideoSushibar
 *
 */
class MyVideoRenderMyVideoSushibar extends MyVideoRendererBase implements MyVideoRendererInterface
{
    const KEY_PART_SUSHIBAR = 'sushibar';

    /**
     * render content object
     */
    public function render()
    {
        $data = $this->getMetaData();
        $default = parent::render();
        //layout identifier
        // TODO handle errors
        $layoutNodeId = $default['fields']['relation_layout']['content']['relation_list'][0]['node_id'];

        if ($layoutNodeId) {
            $layout_node = eZContentObjectTreeNode::fetch($layoutNodeId);
            if ($layout_node instanceof eZContentObjectTreeNode) {
                $layout_datamap = $layout_node->Datamap();
                $data['layout'] = $layout_datamap['identifier']->content();
            }
        } else {
            $data['layout'] = false;
        }

        $data['name'] = $default['fields']['title']['content'];
        $iconDropDownKey = $this->getAttributeValue('icon');
        $data['icon'] = MyVideoicondropdowntype::mapDropDownKeyToChar($iconDropDownKey);

        $mediumFallback = $default['fields']['medium_rectangle_fallback']['content'];
        $mediumExtension = $default['fields']['medium_rectangle_extension']['content'];

        $tmp = array();
        if ($mediumFallback) {
            $this->copyFieldContent('medium_rectangle_fallback_url', $tmp['url']);
            $data['mediumRectangle']['url'] = $tmp['url']['mediumRectangleFallbackUrl'];
            $this->copyFieldContent('medium_rectangle_fallback', $data['mediumRectangle'], false, array('src', 'width', 'height', 'title', 'type', 'alt'));
        } else {
            $data['mediumRectangleFallback'] = null;
        }

        $tmp = array();
        if ($mediumExtension) {
            $this->copyFieldContent('medium_rectangle_extension_url', $tmp['url']);
            $data['mediumRectangleExtension']['url'] = $tmp['url']['mediumRectangleExtensionUrl'];
            $this->copyFieldContent('medium_rectangle_extension', $data['mediumRectangleExtension'], false, array('src', 'width', 'height', 'title', 'type', 'alt'));
        } else {
            $data['mediumRectangleExtension'] = null;
        }

        //videolist node
        $data['videoList'] = false;
        // TODO handle errors
        $videolistNodeId = $default['fields']['relation_videolist']['content']['relation_list'][0]['node_id'];

        if ($videolistNodeId) {
            $videolistNode = eZContentObjectTreeNode::fetch($videolistNodeId);
            $renderer = MyVideoRenderFactory::getInstance($videolistNode->ContentObject);
            $arr = $renderer->render();
            $data['videoList'] = $arr;
        }

        $contentObject = $this->getContentObject();
        $data['url'] = '/_partial/' . self::KEY_PART_SUSHIBAR . '/' . $contentObject->ID;

        return $data;
    }

    protected function fetchKeyPartIdentifier()
    {
        $contentObject = $this->getContentObject();
        return '/_partial/' . self::KEY_PART_SUSHIBAR . '/' . $contentObject->ID;
    }
}
