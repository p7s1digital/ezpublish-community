<?php
/**
 * Render an eZPublish contentType myvideo_sushibar_layout
 */

class MyVideoRenderMyVideoSushibarLayout extends MyVideoRendererBase implements MyVideoRendererInterface
{
    /**
     * render simplyfied version
     *
     */

    public function render()
    {
        $default = parent::render();
        $data = array();
        $data['type'] = $default['type'];
        // TODO implement a helper method
        $data['identifier'] = $default['fields']['identifier']['content'];
        return $data;
    }
}
