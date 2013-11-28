<?php
/**
 * Render an eZPublish contentType article
 */

class MyVideoRenderArticle extends MyVideoRendererBase implements MyVideoRendererInterface
{

    public function render()
    {
        $data = parent::render();
        // HINT, create a simplified version
        // TODO, find a better solution for this hack
        $tmp = $this->getMetaData();
        $tmp['title'] = $data['fields']['title']['content'];
        $tmp['body'] = $data['fields']['body']['content'];

        return $tmp;
    }
}
