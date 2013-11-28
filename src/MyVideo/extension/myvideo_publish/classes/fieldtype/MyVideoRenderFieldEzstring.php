<?php
/**
 * Description of MyVideoRenderFieldEzstring
 *
 */
class MyVideoRenderFieldEzstring extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        $att = $this->getContentObjectAttribute();
        return $att->attribute('data_text');
    }
}
