<?php
/**
 * Renders the MyVideoRenderMyVideoImageGallery
 *
 */
class MyVideoRenderMyVideoImageGallery extends MyVideoRendererBase implements MyVideoRendererInterface
{

    /**
     * render content object
     */
    public function render()
    {
        parent::render();
        $data = array();
        $data['type'] = 'imageGallery';
        $name = $this->getAttributeValue('name');
        $data['id'] = preg_replace('/[^A-Za-z0-9]/', '', $name);
        $this->copyFieldContent('title', $data);
        $this->copyFieldContent('description', $data);
        $data['images'] = $this->renderAssetRelationList('image_list');
        $data['ad'] = $this->renderAssetRelationList('ad', true);
        return $data;
    }
}
