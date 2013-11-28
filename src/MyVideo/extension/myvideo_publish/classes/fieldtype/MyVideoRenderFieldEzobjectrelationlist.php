<?php
/**
 * Description of MyVideoRenderFieldEzezobjectrelationlist
 *
 */

class MyVideoRenderFieldEzobjectrelationlist extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    public function render()
    {
        /** @var eZContentObjectAttribute $att */
        $att = $this->getContentObjectAttribute();
        return $att->content();
    }
}
