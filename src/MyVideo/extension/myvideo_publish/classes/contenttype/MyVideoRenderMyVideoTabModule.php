<?php

/**
 * Render an eZPublish contentType TabModule
 */
class MyVideoRenderMyVideoTabModule extends MyVideoRendererBase implements MyVideoRendererInterface
{

    public function render()
    {
        parent::render();

        $data = array();
        $data['type'] = 'TabModule';
        $data['activeTab'] = 1;
        $data['title'] = $this->getAttributeValue('title');
        $data['modules'] = $this->renderAssetRelationList('modules');

        return $data;
    }
}
