<?php

require 'Predis/Autoloader.php';

class mxdRedis implements mxdKeyValueStoreIF
{

    /**
     * local class variable to store the single instance
     * @var mxdRedis
     */
    protected static $_instance;

    /**
     * the redis hostname
     * @var string
     */
    private $_host;

    /**
     * the redis server port
     * @var integer
     */
    private $_port;

    /**
     * the redis database
     * @var integer
     */
    private $_database;


    /**
     * instance of the redis connect
     * @var Predis\Client
     */
    private $_redis;

    /**
     * private constructor
     * -> singleton
     */
    protected function __construct()
    {
        Predis\Autoloader::register();

        $this->readSettings();
        $this->connect();
    }

    /**
     * create and return a single instance of this class
     *
     * @return mxdRedis
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * connect to the redis server with
     * the settings from file redis.ini
     *
     * @throws Exception
     */
    private function connect()
    {
        $master = array(
            'host' => $this->_host,
            'port' => $this->_port,
            'database' => $this->_database
        );

        try {
            $this->_redis = new Predis\Client($master);
            // try to connect
            $this->_redis->connect();
        } catch (Exception $e) {
            $errMsg = sprintf('Failed to connect to redis server. host: %s port: %d, Text: %s', $this->_host, $this->_port, $e->getMessage());
            eZDebug::writeError($errMsg);
            throw new Exception($errMsg);
        }
    }

    /**
     * read server host and port from redis.ini
     *
     * @throws ezcConfigurationReadFailedException
     */
    private function readSettings()
    {
        $ini = eZINI::instance('environment.ini');

        $this->_host = $ini->variable('RedisSettings', 'Host');
        if (false === $this->_host) {
            throw new ezcConfigurationReadFailedException('Missing variable Server/Host in environment.ini');
        }

        $this->_port = $ini->variable('RedisSettings', 'Port');
        if (false === $this->_port) {
            throw new ezcConfigurationReadFailedException('Missing variable Server/Port in environment.ini');
        }

        $this->_database = $ini->variable('RedisSettings', 'Database');
        if (false === $this->_database) {
            throw new ezcConfigurationReadFailedException('Missing variable Server/Database in environment.ini');
        }
    }

    /**
     * wrapper for the redis SET method
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function set($key, $value)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $result = $this->_redis->set($key, $value);
        return $result;
    }

    /**
     * wrapper for the redis GET method
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $value = $this->_redis->get($key);
        return $value;
    }

    /**
     * remove the giveb key from redis
     *
     * @param string $key
     * @return boolean
     */
    public function delete($key)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $success = $this->_redis->del($key);
        return $success;
    }

    /**
     * Adds an entry to a sorted set
     *
     * @param string $key   The key
     * @param integer $sort  The value to sort on
     * @param string $entry The value to be added to the sorted set
     */
    public function zadd($key, $sort, $entry)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $success = $this->_redis->zadd($key, $sort, $entry);
        return $success;
    }

    /**
     * Removes a value from a sorted set
     *
     * @param string $key   The key
     * @param string $entry The value to be added to the sorted set
     */
    public function zrem($key, $entry)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $success = $this->_redis->zrem($key, $entry);
        return $success;
    }

    public function zrank($key, $index)
    {
        $success = $this->_redis->zrank($key, $index);
        return $success;
    }

    /**
     * Remove all keys from all databases
     *
     * @return boolean
     */
    public function flushAll()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $success = $this->_redis->flushAll();
        return $success;
    }

    /**
     * Synchronously save the dataset to disk
     *
     * @return boolean
     */
    public function save()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $success = $this->_redis->save();
        return $success;
    }

    /**
     * Get Redis Status Information
     *
     * @return array
     */
    public function info()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $result = $this->_redis->info();
        return $result;
    }

    /**
     *
     * @param $key
     * @param $index
     * @param $value
     * @return mixed
     */
    public function hset($key, $index, $value)
    {
        $result = $this->_redis->hset($key, $index, $value);
        return $result;
    }

    /**
     * Fetches a key from a hash entry
     * @param $key
     * @param $index
     * @return mixed
     */
    public function hget($key, $index)
    {
        $result = $this->_redis->hget($key, $index);
        return $result;
    }

    /**
     * Fetches a list of keys from a hash entry.
     * @param $key
     * @param array $indexList
     * @return mixed
     */
    public function hmget($key, array $indexList)
    {
        $result = call_user_func_array(array($this->_redis, 'hmget'), $indexList);
        return $result;
    }

    /**
     * Removes a key in the internal hash
     * @param $key Key of the hash
     * @param $index Key inside the hash
     */
    public function hdel($key, $index)
    {
        $result = $this->_redis->hdel($key, $index);
        return $result;
    }

}
