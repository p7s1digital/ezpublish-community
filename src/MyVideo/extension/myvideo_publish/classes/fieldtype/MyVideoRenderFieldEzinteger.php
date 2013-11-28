<?php
/**
 * Description of MyVideoRenderFieldEzinteger
 *
 */
class MyVideoRenderFieldEzinteger extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        $att = $this->getContentObjectAttribute();
        return $att->content();
    }
}
