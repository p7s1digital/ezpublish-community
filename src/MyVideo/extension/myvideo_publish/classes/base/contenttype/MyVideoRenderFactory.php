<?php
/**
 * Return specific or default renderer
 */
class MyVideoRenderFactory
{

    /**
     * prefix of a content renderer class
     * @var string
     */
    const CONTENT_RENDERER_CLASS_PREFIX = 'MyVideoRender';

    /**
     * private constructor
     */
    private function __construct()
    {
    }

    /**
     * get instance of content type specific renderer class
     *
     * @param $contentObject
     * @return MyVideoRendererInterface
     */
    public static function getInstance($contentObject)
    {
        // HINT always return a new instance!
        $classIdentifier = $contentObject->ClassIdentifier;
        // HINT class_identifier are prefixed with "myvideo_" ...
        // ... so we need to tweak them to get the desired "MyVideo"-CamelCase
        $preparedClassIdentifier = preg_replace('#^myvideo_#', 'my_video_', $classIdentifier);
        $classNameCamelCase = self::CONTENT_RENDERER_CLASS_PREFIX . MyVideoUtils::toCamelCase($preparedClassIdentifier, true);

        if (class_exists($classNameCamelCase)) {
            return new $classNameCamelCase($contentObject);
        }

        return new MyVideoRenderGenericRenderer($contentObject);
    }
}
