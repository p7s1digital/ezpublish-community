<?php

class mxdDTO
{
    protected $_data = array();

    /**
     * indicates if the data has been stored yet, e.g. to database
     * @var boolean
     */
    protected $_isDirty = false;

    /**
     * set the dirty flag
     *
     * @param boolean $dirty
     */
    public function setDirty($dirty)
    {
        $this->_isDirty = (bool)$dirty;
    }

    /**
     * initialize the data by an array
     * used to init the data after reading it from database
     *
     * @param array $data
     * @param boolean $isDirty
     */
    public function initFromArray(array $data, $isDirty = true)
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }
        $this->_isDirty = (bool)$isDirty;
    }

    /**
     * return the value for the given $key
     * if there is no such a key, the method will return false
     *
     * @param string $key
     * @return mixed
     */
    public function getData($key)
    {
        if (!$this->dataIsSet($key)) {
            return false;
        }
        return $this->_data[$key];
    }

    /**
     * check if the given $key exists
     *
     * @param string $key
     * @return boolean
     */
    public function dataIsSet($key)
    {
        return array_key_exists($key, $this->_data);
    }

    /**
     * remove a key/value pair from the data list
     *
     * @param string $key
     */
    public function unsetData($key)
    {
        unset($this->_data[$key]);
        $this->_isDirty = true;
    }

    /**
     * write the key/value pairs to the data array
     *
     * @param string $key
     * @param string $value
     */
    public function setData($key, $value)
    {
        // check if we have to change something
        if (true === $this->dataIsSet($key) && $this->_data[$key] == $value) {
            return;
        }

        $this->_data[$key] = $value;
        $this->_isDirty = true;
    }

    /**
     * check if the given $key exists
     * @see dataIsSet
     *
     * this method is required by eZPublish template system
     *
     * @param string $key
     * @return boolean
     */
    public function hasAttribute($key)
    {
        return $this->dataIsSet($key);
    }

    /**
     * get a list of all attributes in the DTO
     *
     * this method is required by eZPublish template system
     *
     * @return array
     */
    public function attributes()
    {
        return $this->_data;
    }

    /**
     * return the value for the given $key
     * if there is no such a key, the method will return false
     * @see getData
     *
     * this method is required by eZPublish template system
     *
     * @param string $key
     * @return mixed
     */
    public function attribute($key)
    {
        return $this->getData($key);
    }

}
