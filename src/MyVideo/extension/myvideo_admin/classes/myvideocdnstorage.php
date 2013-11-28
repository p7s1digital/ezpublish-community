<?php
/**
 * Safe binary data to a key value riak server
 *
 * Retrieves the binary file from eZ Publish storage and sends it via cUrl to the riak service.
 * The available riak-hosts are defined in the environment.ini
 *
 * USAGE:
 * MyVideoCdnStorage::store($binary_path)
 */

class MyVideoCdnStorage
{

    const logfile = 'myvideo_cdn_storage.log';
    private $itsHost;
    private $itsBinaryPath;
    private $itsTargetPath;
    private $itsTargetPrefix;
    private $itsSourcePrefix;
    private $curlHandle;
    private $port = '8098';

    private function __construct($host)
    {
        $this->setHost($host);
        $ini = eZINI::instance('environment.ini');
        $this->itsSourcePrefix = $ini->variable('RiakSettings', 'SourcePrefix');
        $this->itsTargetPrefix = $ini->variable('RiakSettings', 'TargetPrefix');
    }

    public function setHost($host)
    {
        $this->itsHost = $host;
    }

    public static function store($path)
    {
        $targetHosts = self::getTargetHosts();
        $success = false;
        $host = null;
        self::log('try to store binary ' . $path);
        while (!$success && ($target = array_shift($targetHosts))) {
            $success = $target->putBinary($path);
        }

        if ($success) {
            $host = $target->getHost();
            self::log('binary saved to host ' . $host . 'binary saved to ' . $target->getTargetPath());

            return $target->getTargetPath();
        } else {
            self::log('FAIL binary not saved');

            return false;
        }
    }

    private static function getTargetHosts()
    {
        $ini = eZINI::instance('environment.ini');
        $hosts = $ini->variable('RiakSettings', 'Hosts');
        $stack = array();
        foreach ($hosts as $host) {
            $stack[] = new MyVideoCdnStorage($host);
        }

        return $stack;
    }

    public static function log($msg)
    {
        eZLog::write($msg, self::logfile, 'var/log');
    }

    public function putBinary($path)
    {
        $this->setBinaryPath($path);
        $absolutePath = $this->getAbsoluteBinaryPath();
        if (!is_readable($absolutePath)) {
            self::log('FAILURE can not read binary ' . $absolutePath);

            return false;
        }

        $url = sprintf('http://%s:' . $this->port . '/riak/', $this->itsHost) . $this->getTargetPath();
        $this->curlHandle = curl_init();
        $file = $this->getAbsoluteBinaryPath();
        $fileHandle = fopen($file, "rb");

        curl_setopt($this->curlHandle, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($this->curlHandle, CURLOPT_FORBID_REUSE, true);
        curl_setopt($this->curlHandle, CURLOPT_VERBOSE, false);
        curl_setopt($this->curlHandle, CURLOPT_HEADER, false);
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        curl_setopt($this->curlHandle, CURLOPT_PUT, true);
        curl_setopt($this->curlHandle, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_INFILE, $fileHandle);
        curl_setopt($this->curlHandle, CURLOPT_INFILESIZE, filesize($file));
        curl_setopt($this->curlHandle, CURLOPT_USERAGENT, 'ez riak uploader');
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, array(
                'Content-Type:' . mime_content_type($file))
        );

        curl_exec($this->curlHandle);
        fclose($fileHandle);
        $success = $this->logRiakErrors($url);

        return $success;
    }

    public function setBinaryPath($path)
    {
        $this->itsBinaryPath = $path;
    }

    public function getAbsoluteBinaryPath()
    {
        $path = $this->getBinaryPath();
        if (!preg_match('#^/#', $path)) {
            $path = $this->getBinarySourceDir() . '/' . $path;
        }

        return $path;
    }

    public function getBinaryPath()
    {
        return $this->itsBinaryPath;
    }

    private function getBinarySourceDir()
    {
        return eZSys::rootDir();
    }

    public function getTargetPath()
    {
        $binaryPath = $this->getBinaryPath();
        $tmp = preg_replace('#^' . preg_quote($this->itsSourcePrefix) . '#', '', $binaryPath);

        return $this->itsTargetPrefix . '/' . str_replace('/', '-', $tmp);
    }

    private function logRiakErrors($url)
    {
        $errno = curl_errno($this->curlHandle);
        $status = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);

        if ($errno > 0) {
            $error = curl_error($this->curlHandle);
            curl_close($this->curlHandle);
            self::log(sprintf('Connection to riak failed, curl said: "%s" ', $error));
        } else {
            curl_close($this->curlHandle);
            if ($status >= 400) {
                self::log("Riak error code: $status at url:" . $url);
            } else {
                self::log("file uploaded ($url).");
                return true;
            }
        }
        return false;
    }

    public function getHost()
    {
        return $this->itsHost;
    }

    public function setTargetPath($path)
    {
        $this->itsTargetPath = $path;
    }
}
