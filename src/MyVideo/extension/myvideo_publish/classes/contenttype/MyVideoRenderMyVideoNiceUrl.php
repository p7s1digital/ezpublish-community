<?php
/**
 * Render class for an alias of an URL
 * User: patrik jacoby
 * Date: 25.06.13
 */

class MyVideoRenderMyVideoNiceUrl extends MyVideoRendererBase implements MyVideoRendererInterface
{
    const KEY_PART_TYPE = 'redirect';

    /**
     *
     * @return array
     */
    public function render()
    {
        parent::render();

        $data = array();
        $data['type'] = 'niceUrl';
        $data['niceUrls'] = array();
        $urlList = $this->getAttributeValue('nice_urls');
        $this->copyFieldContent('redirect_url', $data);
        $targetUrl = $data['redirectUrl']['url'];
        foreach ($urlList as $niceUrl) {
            if (strlen($niceUrl['col_0']) > 0) {
                $data['niceUrls'] [] = $niceUrl['col_0'];
                $this->addRedirectsToRedis($niceUrl['col_0'], $targetUrl);
            }
        }
        return array('url' => $targetUrl);
    }

    /**
     * @see MyVideoRendererInterface::handleUpdate
     *
     */
    public function handleUpdate($prePublishData)
    {
        // first we try to fetch the last version, as we have to check for changed redis keys
        $lastVersion = MyVideoUtils::fetchLastObjectData($this->getContentObject(), $this->getLanguage());

        if (true === is_null($lastVersion)) {
            // if no previous version is found, we do not need to remove a redis key
            return;
        }
        // we have to remove some keys from redis
        $redis = new MyVideoStorageRedis();
        $attributeList = $this->getAttributeValue('nice_urls');
        $urlList = $this->retrieveUrls($attributeList);
        $previousValues = $lastVersion['nice_urls'];
        $previousUrlList = $this->retrieveUrls($previousValues);

        foreach ($previousUrlList as $urlToCheck) {
            if (false === in_array($urlToCheck, $urlList)) {
                $redisKey = $this->buildKey($urlToCheck);
                $redis->delete($redisKey);
            }
        }
    }

    /**
     * @see MyVideoRendererInterface::handleDelete
     */
    public function handleDelete($node)
    {
        $attributeList = $this->getAttributeValue('nice_urls');
        $urlList = $this->retrieveUrls($attributeList);
        $redis = new MyVideoStorageRedis();
        foreach ($urlList as $urlToCheck) {
            $redisKey = $this->buildKey($urlToCheck);
            $redis->delete($redisKey);
        }
    }

    /**
     *
     */
    private function removeRedisKeysFromLastVersion()
    {
        // first we try to fetch the last version, as we have to check for changed redis keys
        $lastVersion = MyVideoUtils::fetchLastObjectData($this->getContentObject(), $this->getLanguage());

        if (true === is_null($lastVersion)) {
            // if no previous version is found, we do not need to remove a redis key
            return;
        }
        // we have to remove some keys from redis
        $redis = new MyVideoStorageRedis();
        $attributeList = $this->getAttributeValue('nice_urls');
        $urlList = $this->retrieveUrls($attributeList);
        $previousValues = $lastVersion['nice_urls'];
        $previousUrlList = $this->retrieveUrls($previousValues);

        foreach ($previousUrlList as $urlToCheck) {
            if (false === in_array($urlToCheck, $urlList)) {
                $redisKey = $this->buildKey($urlToCheck);
                $redis->delete($redisKey);
            }
        }
    }

    /**
     * Part of redis key.
     *
     * @return string
     */
    protected function fetchKeyPartVisibility()
    {
        return 'live';
    }

    /**
     * Writes a single redis key for an alias entry
     *
     * @param $aliasUrl string The alias url for the redirect
     * @param $targetUrl string The url to redirect to
     */
    private function addRedirectsToRedis($aliasUrl, $targetUrl)
    {
        $redis = new MyVideoStorageRedis();
        $redisKey = $this->buildKey($aliasUrl);
        $redis->setKey($redisKey);
        $value = json_encode(array('url' => $targetUrl));
        $redis->store($value);
    }

    /**
     * Generates a redis key for a redirect on the an alias entry
     *
     * @param $aliasUrl string
     * @return string|void
     */
    protected function buildKey($aliasUrl = null)
    {
        if (is_null($aliasUrl)) {
            $aliases = $this->getAttributeValue('nice_urls');
            $aliasUrl = $aliases[0]['col_0'];
        }
        $keyParts = array();
        $keyParts [] = self::KEY_PART_TYPE;
        $keyParts [] = $this->fetchKeyPartVersion();
        $keyParts [] = $this->fetchKeyPartDomain();
        $keyParts [] = $this->fetchKeyPartLanguage();
        $keyParts [] = 'live';
        $keyParts [] = $aliasUrl;

        return implode(self::KEY_DELIMITER, $keyParts);
    }

    private function retrieveUrls(array &$attributeList)
    {
        $urlList = array();
        foreach ($attributeList as $attribute) {
            $urlList [] = $attribute['col_0'];
        }
        return $urlList;
    }
}
