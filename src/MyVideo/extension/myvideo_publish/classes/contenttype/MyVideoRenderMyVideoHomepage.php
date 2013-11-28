<?php
/**
 * Render an eZPublish contentType myvideo_homepage
 */

class MyVideoRenderMyVideoHomepage extends MyVideoRendererBase implements MyVideoRendererInterface
{

    public function fetchKey_deprecated($node = null)
    {
        $language = $this->getLanguage();

        // TODO is this required?
        $node = eZContentObjectTreeNode::fetch($node->NodeID, $language);
        if (!is_object($node)) {
            return 'FAULT-NO-KEY-FOR-' . $language;
        }

        $itsIdentifier = '/MyVideo/';

        // TODO use default language?
        // TODO log and/or throw exception
        if (is_null($language)) {
            $language = 'LANGUAGE-NOT-SET';
        }

        // TODO key composition (stage:locale:slug)
        $key = join(':', array('content', $language, $itsIdentifier));

        return $key;
    }

    public function render()
    {
        $data = parent::render();
        // HINT, create a simplified version
        // TODO, find a better solution for this hack
        $tmp = array();
        $tmp['type'] = $data['type'];
        $tmp['tracking'] = $this->renderMyVideoTrackingData();
        $tmp['metaData'] = $this->renderMyVideoMetaData();
        $tmp['stage'] = $this->renderStage();
        $tmp['sushibars'] = $this->renderSushibars();
        $tmp = $this->renderConfig($tmp);
        $data = $tmp;

        return $data;
    }

    /**
     * Insert the config attributes into the group sections in the homepage data for the redis key
     * @param array $data
     * @return array
     */
    private function renderConfig(array $data)
    {
        $filterList = array(
            'ClassFilterType' => 'include',
            'Depth', 10,
            'ClassFilterArray' => array('myvideo_config')
        );

        $subTreeNodeId = eZINI::instance('content.ini')->variable('NodeSettings', 'RootNode');
        $nodeList = eZContentObjectTreeNode::subTreeByNodeID($filterList, $subTreeNodeId);

        $contentObject = $nodeList[0]->ContentObject;

        $renderer = new MyVideoRenderMyVideoConfig($contentObject, MyVideoRenderer::VISIBILITY_MODE_LIVE);
        $fetchedData = $renderer->getConfigData();

        foreach ($fetchedData as $groupName => $items) {
            foreach ($items as $identifier => $item) {
                $data[$groupName][$identifier] = $item;
            }
        }

        return $data;
    }

    private function renderStage()
    {
        $data = parent::render();
        // TODO implement herlper method
        $arr = $data['fields']['stage']['content']['relation_list'];
        $item = $arr[0];
        /** @var eZContentObjectTreeNode $itemNode */
        $itemNode = eZContentObjectTreeNode::fetch($item['node_id']);
        /** @var eZContentObject $contentObject */
        $contentObject = $itemNode->object();
        $renderer = MyVideoRenderFactory::getInstance($contentObject);

        return $renderer->render();
    }

    private function renderSushibars()
    {
        $buffer = array();
        $data = parent::render();
        // TODO implement herlper method
        $arr = $data['fields']['sushibars']['content']['relation_list'];

        foreach ($arr as $item) {
            /** @var eZContentObjectTreeNode $itemNode */
            $itemNode = eZContentObjectTreeNode::fetch($item['node_id']);
            if (is_object($itemNode)) {
                /** @var eZContentObject $contentObject */
                $contentObject = $itemNode->object();
            }

            if ($itemNode === null || $itemNode->IsInvisible || !$this->isContentObjectOnline($contentObject)) {
                continue;
            }

            $renderer = MyVideoRenderFactory::getInstance($itemNode->object());
            $buffer[] = $renderer->render();
        }

        return $buffer;
    }

    protected function fetchKeyPartIdentifier()
    {
        // TODO implement - this is mock up only
        return '/';
    }
}
