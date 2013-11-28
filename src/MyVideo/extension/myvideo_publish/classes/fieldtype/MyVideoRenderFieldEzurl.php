<?php
/**
 * Description of MyVideoRenderFieldEzurl
 *
 */
class MyVideoRenderFieldEzurl extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        $att = $this->getContentObjectAttribute();
        $label = $att->attribute('data_text');
        $url = $att->content();
        $data = array('label' => $label, 'url' => $url);
        return $data;
    }
}
