<?php
/**
 * Description of MyVideoRenderFieldEzurl
 *
 */
class MyVideoRenderFieldEzkeyword extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        /** @var eZContentObjectAttribute $att */
        $att = $this->getContentObjectAttribute();

        /** @var eZKeyword $data */
        $data = $att->content();
        if ($data instanceof eZKeyword) {
            return $data->keywordArray();
        }

        return null;
    }
}
