<?php
/**
 * Description of MyVideoRenderFieldEzinteger
 *
 */
class MyVideoRenderFieldEzfloat extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{
    public function render()
    {
        $att = $this->getContentObjectAttribute();
        return (float)$att->content();
    }
}
