<?php


class MyVideoRenderMyVideoTextTeaser extends MyVideoRendererBase implements MyVideoRendererInterface
{

    /**
     * render content object
     */
    public function render()
    {
        parent::render();

        $data = array();

        $data['type'] = 'textTeaser';

        $data['title'] = $this->getAttributeValue('title');
        $data['description'] = $this->getAttributeValue('description');

        return $data;
    }
}
