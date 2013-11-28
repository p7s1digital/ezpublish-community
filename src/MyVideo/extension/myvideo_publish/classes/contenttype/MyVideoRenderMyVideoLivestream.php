<?php
/**
 * Description of MyVideoRenderMyVideoImageTeaser
 *
 */
class MyVideoRenderMyVideoLivestream extends MyVideoRendererBase implements MyVideoRendererInterface
{

    const REDIS_KEY_IDENTIFIER = 'myvideo-counter';

    /**
     * render all
     *
     * @return array|null
     */
    public function render()
    {
        return $this->renderAll();
    }

    /**
     * Render all content objects
     *
     * @return array|null
     */
    private function renderAll()
    {
        $data = array();
        $data['type'] = 'livestreamCounter';
        $data['items'] = array();
        $filterList = array(
            'ClassFilterType' => 'include',
            'Depth', 10,
            'ClassFilterArray' => array('myvideo_livestream'),
            'SortBy' => array('attribute', eZContentObjectTreeNode::SORT_ORDER_ASC, 'myvideo_livestream/publishing_time'),
            'AttributeFilter' => array(array('myvideo_livestream/end_time',
                '>',
                time()))
        );
        $subTreeNodeId = eZINI::instance('content.ini')->variable('NodeSettings', 'MediaRootNode');
        $nodeList = eZContentObjectTreeNode::subTreeByNodeID($filterList, $subTreeNodeId);

        foreach ($nodeList as $node) {
            if ($node->IsInvisible || !$this->isContentObjectOnline($node->ContentObject)) {
                continue;
            }

            $this->setContentObject($node->ContentObject);
            $data['items'][] = $this->renderOne();
        }

        if (empty($data['items'])) {
            $data['items'] = array();
        }

        return $data;
    }

    /**
     * render content object
     */
    private function renderOne()
    {
        $data = array();
        $data['type'] = 'livestream';

        $this->copyFieldContent('title', $data);
        $this->copyFieldContent('subtitle', $data);
        $this->copyFieldContent('image', $data, false, array('src'));
        $this->copyFieldContent('url', $data);
        $this->copyFieldContent('publishing_time', $data, true);
        $this->copyFieldContent('start_time', $data, true);
        $this->copyFieldContent('end_time', $data, true);

        return $data;
    }

    /**
     * use remote id as identifier part for redis key generation
     * @return string
     */
    protected function fetchKeyPartIdentifier()
    {
        return self::KEY_PREFIX . self::REDIS_KEY_IDENTIFIER;
    }

    /**
     * render the redis without the deleted node
     * @see MyVideoRendererInterface::handleDelete
     */
    public function handleDelete($node)
    {
        if ($node instanceof eZContentObjectTreeNode) {
            $contentObject = $node->ContentObject;
            foreach ($contentObject->availableLanguages() as $language) {
                $renderer = MyVideoRenderFactory::getInstance($contentObject);
                $renderer->setLanguage($language);
                $data = $renderer->render();
                $json = json_encode($data);
                $renderer->storePrimaryRedisData($json);
            }
        }
    }

    /**
     * @see MyVideoRendererInterface::deletePrimaryRedisData
     */
    public function deletePrimaryRedisData()
    {
        // do nothing
    }
}
