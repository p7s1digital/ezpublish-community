<?php

/**
 * Interface which must be implemented by MyVideoRender classes
 */

interface MyVideoRendererInterface
{
    /**
     * Aggregates the data of a content node, so the data can be written into redis.
     *
     * @return mixed
     */
    public function render();

    /**
     * Set the language
     *
     * @param $s
     */
    public function setLanguage($s);

    /**
     * Get the language currently set
     *
     * @return string
     */
    public function getLanguage();

    /**
     * Generates the redis key of the object
     *
     * @return string
     */
    public function fetchKey();

    /**
     * Stores the primary redis data
     *
     * @param $json
     */
    public function storePrimaryRedisData($json);

    /**
     * Deletes the primary redis data
     */
    public function deletePrimaryRedisData();

    /**
     * Executes additional logic, when a content element is updated, f.e. writing additional redis keys
     *
     * @param array $prePublishData
     */
    public function handleUpdate($prePublishData);

    /**
     * Executes additional logic, when a content element is deleted or unpublished, f.e. removing redis keys
     */
    public function handleDelete($node);
}
