<?php
/**
 * Description of MyVideoFieldRendererBase
 *
 */
class MyVideoFieldRendererBase implements MyVideoFieldRendererInterface
{
    /** @var eZContentObject */
    public $ContentObject = null;
    /** @var eZContentObjectAttribute */
    public $contentObjectAttribute = null;
    /** @var string */
    private $language = null;

    /**
     * Set the Attribute of the current content object
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     */
    public function __construct($contentObjectAttribute)
    {
        $this->contentObjectAttribute = $contentObjectAttribute;
    }

    /**
     * Return the attribute value
     *
     * @return mixed
     */
    public function render()
    {
        $att = $this->getContentObjectAttribute();
        return $att->toString();
    }

    /**
     * Get the attribute of the current content object
     *
     * @return eZContentObjectAttribute
     */
    public function getContentObjectAttribute()
    {
        return $this->contentObjectAttribute;
    }

    /**
     * Get the current language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the current language
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Field by default do not have a redis key
     *
     * @return string|null
     */
    public function fetchKey()
    {
        return null;
    }
}
