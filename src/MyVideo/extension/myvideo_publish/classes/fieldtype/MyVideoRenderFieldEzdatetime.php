<?php
/**
 * Description of MyVideoRenderFieldEzdatetime
 *
 */
class MyVideoRenderFieldEzdatetime extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        $attr = $this->getContentObjectAttribute();
        return $attr->attribute('data_int');
    }
}
