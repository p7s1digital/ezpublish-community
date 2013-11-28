<?php
/**
 * Save data to redis
 */

class MyVideoStorageRedis
{

    private $key = 'A-USEFUL-KEY-IS-MISSING';

    /**
     * Store the given data to the redis
     *
     * @param $data
     * @return string
     */
    public function store($data)
    {
        try {
            $redis = $this->getRedisHandler();
            $redis->set($this->key, $data);

            return $this->key;
        } catch (Exception $e) {
            print $e->getMessage();
            eZExecution::cleanExit();
        }
    }

    /**
     * Delete the given data from the redis
     *
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        try {
            $redis = $this->getRedisHandler();
            $success = $redis->delete($key);

            return $success;
        } catch (Exception $e) {
            print $e->getMessage();
            eZExecution::cleanExit();
        }
    }

    /**
     * Set given key
     *
     * @param $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Get the redis instance
     *
     * @return mxdRedis
     */
    private function getRedisHandler()
    {
        try {
            return $redis = mxdRedis::getInstance();
        } catch (Exception $e) {
            print $e->getMessage();
            ezExecution::cleanExit();
        }
    }
}
