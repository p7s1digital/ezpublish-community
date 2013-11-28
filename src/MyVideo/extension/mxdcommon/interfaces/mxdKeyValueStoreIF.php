<?php

interface mxdKeyValueStoreIF
{

    /**
     * wrapper for the redis SET method
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function set($key, $value);

    /**
     * wrapper for the redis GET method
     *
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * remove the giveb key from redis
     *
     * @param string $key
     * @return boolean
     */
    public function delete($key);

    /**
     * Remove all keys from all databases
     *
     * @return boolean
     */
    public function flushAll();

    /**
     * Synchronously save the dataset to disk
     *
     * @return boolean
     */
    public function save();

}
