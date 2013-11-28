<?php
/**
 * Generic default renderer if no contentType specific renderer is implemented
 */

class MyVideoRenderGenericRenderer extends MyVideoRendererBase implements MyVideoRendererInterface
{

    /**
     * Log the current called method
     *
     * @param $contentObject
     */
    public function __construct($contentObject)
    {
        parent::__construct($contentObject);
        mxdLog::write('TODO implement content renderer for "' . $contentObject->ClassIdentifier . '"');
    }
}
