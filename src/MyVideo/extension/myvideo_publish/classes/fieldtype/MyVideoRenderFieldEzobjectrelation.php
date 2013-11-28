<?php
/**
 * Description of MyVideoRenderFieldEzezobjectrelationlist
 *
 */

class MyVideoRenderFieldEzobjectrelation extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    /**
     * Renders an object relation by getting the related object and passing it to its renderer.
     *
     * @return mixed
     */
    public function render()
    {
        /** @var eZContentObjectAttribute $att */
        $att = $this->getContentObjectAttribute();
        if ($att && $att->content()) {

            $renderer = MyVideoRenderFactory::getInstance($att->content());
            return $renderer->render();
        }

        return null;
    }
}
