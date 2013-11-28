<?php
/**
 * Generic default renderer if no fieldType specific renderer is implemented
 */

class MyVideoRenderFieldGenericRenderer extends MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{

    /**
     * Log the current called method
     *
     * @param $contentObjectAttribute
     */
    public function __construct($contentObjectAttribute)
    {
        parent::__construct($contentObjectAttribute);
        mxdLog::write('TODO implement field renderer for "' . $contentObjectAttribute->DataTypeString . '"');
    }
}
