<?php

/**
 * Interface which must be implemented by Field-Renderer classes
 */
interface MyVideoFieldRendererInterface
{
    /**
     * Get the attribute of the current content object
     *
     * @return eZContentObjectAttribute
     */
    public function getContentObjectAttribute();

    /**
     * Return the attribute value
     *
     * @return mixed
     */
    public function render();

    /**
     * Get the current language
     *
     * @return string
     */
    public function getLanguage();

    /**
     * Set the current language
     *
     * @param string $language
     */
    public function setLanguage($language);
}
