<?php
/**
 * Description of MyVideoRenderFieldEzboolean
 *
 */
class MyVideoRenderFieldEzboolean extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{
    public function render()
    {
        $att = $this->getContentObjectAttribute();
        $content = (bool) $att->attribute('data_int');
        return $content;
    }
}
