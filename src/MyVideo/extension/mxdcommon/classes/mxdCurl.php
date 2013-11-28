<?php

class mxdCurl
{

    /**
     * CURL RESOURCE
     * @var resource
     */
    private $_curl = null;

    /**
     * CURL setting
     * @var boolean
     */
    private $_curloptFailOnError = false;

    /**
     * CURL setting
     * @var boolean
     */
    private $_curloptFollowLocation = true;

    /**
     * CURL setting
     * @var boolean
     */
    private $_curloptReturnTransfer = true;

    /**
     * CURL setting
     * @var integer
     */
    private $_curloptTimeout = 10;

    /**
     * CURL setting
     * @var boolean
     */
    private $_curloptPost = true;

    /**
     * CURL setting
     * @var string Curl browser identification
     */
    private $_curloptUserAgant = " mozilla/4.0 (compatible; msie 7.0; windows nt 5.1; .net clr 3.0.04506.30;)";

    /**
     * the curlopt array
     * @var array $_optarray
     */
    private $_curloptArray = null;

    /**
     * the result content
     * @var string $_content
     */
    private $_response;

    /**
     * the given url
     * @var string $url
     */
    private $_url;

    /**
     * the given data for post, put and delete
     * @var string $data
     */
    private $_data;

    /**
     * __constructor
     *
     */
    public function __construct()
    {

        $this->_curl = curl_init();
    }

    /**
     * Send the parameter text via GET
     *
     * @return string $content, otherwise null
     */
    public function get()
    {

        // If the Curl options are setted by Array, then get them, otherwise get the defaults
        if (is_null($this->_curloptArray)) {
            curl_setopt($this->_curl, CURLOPT_URL, $this->getUrl());
            curl_setopt($this->_curl, CURLOPT_FAILONERROR, $this->_curloptFailOnError);
            curl_setopt($this->_curl, CURLOPT_FOLLOWLOCATION, $this->_curloptFollowLocation);
            curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, $this->_curloptReturnTransfer);
            curl_setopt($this->_curl, CURLOPT_TIMEOUT, $this->getTimeout());
            curl_setopt($this->_curl, CURLOPT_USERAGENT, $this->_curloptUserAgant);
        }

        return $this->execute();
    }

    /**
     * Send the parameter text via POST
     *
     * @return string $content, otherwise null
     */
    public function post()
    {
        // If the Curl options are setted by Array, then get them, otherwise get the defaults
        if (is_null($this->_curloptArray)) {
            curl_setopt($this->_curl, CURLOPT_URL, $this->getUrl());
            curl_setopt($this->_curl, CURLOPT_TIMEOUT, $this->getTimeout());
            curl_setopt($this->_curl, CURLOPT_HEADER, 0);
            curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $this->_data);
            curl_setopt($this->_curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($this->_curl, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, $this->_curloptReturnTransfer);
            curl_setopt($this->_curl, CURLOPT_USERAGENT, $this->getUserAgent());
        }

        return $this->execute();
    }

    /**
     * Send the parameter text via PUT
     *
     * @return string $content, otherwise null
     */
    public function put()
    {
        // If the Curl options are setted by Array, then get them, otherwise get the defaults
        if (is_null($this->_curloptArray)) {
            curl_setopt($this->_curl, CURLOPT_URL, $this->getUrl());
            curl_setopt($this->_curl, CURLOPT_TIMEOUT, $this->getTimeout());
            curl_setopt($this->_curl, CURLOPT_HEADER, 0);
            curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $this->_data);
            curl_setopt($this->_curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($this->_curl, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, $this->_curloptReturnTransfer);
            curl_setopt($this->_curl, CURLOPT_USERAGENT, $this->getUserAgent());
        }

        return $this->execute();
    }

    /**
     * Send the parameter text via DELETE
     *
     * @return string $content, otherwise null
     */
    public function delete()
    {
        // If the Curl options are setted by Array, then get them, otherwise get the defaults
        if (is_null($this->_curloptArray)) {
            curl_setopt($this->_curl, CURLOPT_URL, $this->getUrl());
            curl_setopt($this->_curl, CURLOPT_TIMEOUT, $this->getTimeout());
            curl_setopt($this->_curl, CURLOPT_HEADER, 0);
            curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $this->_data);
            curl_setopt($this->_curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($this->_curl, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, $this->_curloptReturnTransfer);
            curl_setopt($this->_curl, CURLOPT_USERAGENT, $this->getUserAgent());
        }

        return $this->execute();
    }

    /**
     * set the url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->_url = $url;
    }

    /**
     * get the url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * set the data for post, put and delete
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * Function to set the request params over array
     * In php5 there is a function to do this, in PHP4 we made in manually
     *
     * @param $arr
     */
    public function setOptArray($arr)
    {
        $this->_curloptArray = $arr;
        foreach ($this->_curloptArray as $key => $option) {
            curl_setopt($this->_curl, $key, $option);
        }
    }

    /**
     * Set additional curl option to request handler
     * @param $key
     * @param $value
     * @return void
     */
    public function setOption($key, $value)
    {
        curl_setopt($this->_curl, $key, $value);
    }

    /**
     * set the timeout in seconds
     *
     * @param int $timeout
     */
    function setTimeout($timeout)
    {
        $this->_curloptTimeout = $timeout;
    }

    /**
     * return the timeout
     *
     * @return number
     */
    public function getTimeout()
    {
        return $this->_curloptTimeout;
    }

    /**
     * set the useragent
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->_curloptUserAgant = $userAgent;
    }

    /**
     * return the useragent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->_curloptUserAgant;
    }

    /**
     * execute the request
     *
     * @return string $content
     * @throws Exception
     */
    protected function execute()
    {
        $this->_response['pages'] = curl_exec($this->_curl);
        $this->_response['error'] = curl_errno($this->_curl);
        $this->_response['ermsg'] = curl_error($this->_curl);

        curl_close($this->_curl);

        $lastError = $this->getLastError();
        if (false !== $lastError) {
            throw new Exception($lastError);
        }

        return $this->_response['pages'];
    }

    /**
     * return the last error
     *
     * @return bool|string
     */
    public function getLastError()
    {
        if (isset($this->_response) && is_array($this->_response) && $this->_response['error'] != 0) {
            return $this->getUrl() . ':' . $this->_response['ermsg'] . ' (' . $this->_response['error'] . ')';
        }

        return false;
    }
}
