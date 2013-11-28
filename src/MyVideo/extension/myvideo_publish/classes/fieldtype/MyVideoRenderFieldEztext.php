<?php
/**
 * Description of MyVideoRenderFieldEztext
 *
 */
class MyVideoRenderFieldEztext extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        $att = $this->getContentObjectAttribute();
        return $att->content();
    }
}
