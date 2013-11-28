<?php
/**
 * Return specific or default renderer
 */
class MyVideoFieldRendererFactory
{
    /**
     * prefix of a content renderer class
     */
    const CONTENT_RENDERER_CLASS_PREFIX = 'MyVideoRenderField';

    /**
     * private constructor
     */
    private function __construct()
    {
    }

    /**
     * get instance of content field type specific renderer class
     *
     * @param $contentObjectAttribute
     * @return MyVideoFieldRendererInterface
     */
    public static function getInstance($contentObjectAttribute)
    {
        $fieldTypeIdentifier = $contentObjectAttribute->DataTypeString;
        $className = self::CONTENT_RENDERER_CLASS_PREFIX . ucfirst($fieldTypeIdentifier);
        $classNameGeneric = self::CONTENT_RENDERER_CLASS_PREFIX . 'GenericRenderer';

        if (class_exists($className)) {
            $class = new $className($contentObjectAttribute);
            return $class;
        } elseif (class_exists($classNameGeneric)) {
            return new $classNameGeneric($contentObjectAttribute);
        }
    }
}
