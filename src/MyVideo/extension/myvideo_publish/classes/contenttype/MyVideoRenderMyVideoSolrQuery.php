<?php
/**
 * Render an eZPublish contentType solr_query
 */

class MyVideoRenderMyVideoSolrQuery extends MyVideoRendererBase implements MyVideoRendererInterface
{
    /**
     * render simplyfied version
     *
     */

    public function render()
    {
        $default = parent::render();

        $data = array();
        $data['type'] = 'VideoQuery';
        $data['contentType'] = $default['fields']['type']['content'];
        $data['attribute'] = $default['fields']['attribute']['content'];
        $data['genre'] = $default['fields']['genre']['content'];
        $data['sortOrder'] = $default['fields']['order']['content'];

        return $data;
    }
}
