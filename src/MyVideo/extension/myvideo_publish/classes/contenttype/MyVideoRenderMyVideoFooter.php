<?php
/**
 * Render an eZPublish contentType myvideo_footer
 */

class MyVideoRenderMyVideoFooter extends MyVideoRendererBase implements MyVideoRendererInterface
{
    const DEFAULT_FOOTER = 'Default';

    private static $channelMap = array(
        'Homepage' => 'Homepage',
        'Tagpage' => 'Tagpage'
    );

    /**
     * Get mapped value of the selected option attribute
     *
     * @see $channelMap
     *
     * @return string
     */
    protected function getChannelAreaMapped()
    {
        $topicData = $this->getAttributeValue('redis_key');
        $topic = isset($topicData['selected_values'][0]) ? $topicData['selected_values'][0] : null;

        if (array_key_exists($topic, self::$channelMap)) {
            return self::$channelMap[$topic];
        }

        return self::DEFAULT_FOOTER;
    }

    /**
     * Generates the last part of the redis key, f.e. '/Footer/Tagpage'
     *
     * @return string
     */
    protected function fetchKeyPartIdentifier()
    {
        return '/_partial/' . MyVideoRenderer::KEY_PREFIX_FOOTER . '/' . $this->getChannelAreaMapped();
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

        $this->copyFieldContent('title', $data);
        $data['info'] = $standard['fields']['copy']['content'];

        $node = $this->ContentObject->attribute('main_node');

        $buffer = array();
        foreach ($node->children() as $child) {

            if ($child->ClassIdentifier != 'myvideo_article') {
                continue;
            }

            // HINT skip if node is invisible ...
            if ($child->IsInvisible || !$this->isContentObjectOnline($child->ContentObject)) {
                continue;
            }
            $renderer = MyVideoRenderFactory::getInstance($child->ContentObject);
            $buffer[] = $renderer->render();
        }

        $data['items'] = $buffer;

        return $data;
    }
}
