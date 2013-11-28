<?php

/**
 * Class MyVideoPublishFunctionCollection
 * Contains fetch functions
 */
class MyVideoPublishFunctionCollection
{
    /**
     * Returns a full HTML snippet of the rendered json
     *
     * @param $node_id
     * @param string $language
     * @return array
     */
    public static function fetchRenderedJSON($node_id, $language = 'ger-DE')
    {
        $node = eZContentObjectTreeNode::fetch($node_id);
        if ($node instanceof eZContentObjectTreeNode) {
            $renderer = MyVideoRenderFactory::getInstance($node->object());
            $renderer->setLanguage($language);
            $data = json_encode($renderer->render());
        }
        $key = 'N.N.';
        $tpl = eZTemplate::factory();
        $tpl->setVariable('vars', array('key' => $key, 'data' => $data));
        $content = $tpl->fetch('design:parts/myvideo_admin/redis_preview.tpl');

        return array('result' => $content);
    }

    /**
     * Returns a full HTML snippet of the fetched JSON
     *
     * @param $node_id
     * @param string $language
     * @return array
     */
    public static function fetchJSON($node_id, $language = 'ger-DE')
    {
        $node = eZContentObjectTreeNode::fetch($node_id);
        if ($node instanceof eZContentObjectTreeNode) {
            $renderer = MyVideoRenderFactory::getInstance($node->object());
            $renderer->setLanguage($language);
            $key = $renderer->fetchKey();
        }

        try {
            $redis = mxdRedis::getInstance();
            $data = $redis->get($key);

            if (is_null($data)) {
                $data = 'NULL';
            }
        } catch (Exception $e) {
            $data = $e->getMessage();
        }

        $tpl = eZTemplate::factory();
        $tpl->setVariable('vars', array('key' => $key, 'data' => $data));
        $content = $tpl->fetch('design:parts/myvideo_admin/redis_preview.tpl');

        return array('result' => $content);
    }
}
