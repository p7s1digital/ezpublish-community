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
        $this->copyFieldContent('is_ugc_backend', $data, true);
        $url = $this->getAttributeValue('redirect_url');
        $url = $url['url'];
        $data['url'] = $url;
        $urlList = $this->getAttributeValue('nice_urls');
        $urlList = $this->getAliasUrls($urlList);
        foreach ($urlList as $niceUrl) {
            if (strlen($niceUrl) > 0) {
                $data['niceUrls'][] = $niceUrl;
            }
        }
        $this->addRedirectsToRedis($data);
        return $data;
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
        $urlList = $this->getAliasUrls($attributeList);
        $previousValues = $lastVersion['nice_urls'];
        $previousUrlList = $this->getAliasUrls($previousValues);

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
        $urlList = $this->getAliasUrls($attributeList);
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
        $urlList = $this->getAliasUrls($attributeList);
        $previousValues = $lastVersion['nice_urls'];
        $previousUrlList = $this->getAliasUrls($previousValues);

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
     * @param $data array The alias data url for the redirect
     */
    private function addRedirectsToRedis($data)
    {
        $redis = new MyVideoStorageRedis();

        foreach ($data['niceUrls'] as $aliasUrl) {
            $redisKey = $this->buildKey($aliasUrl);
            $redis->setKey($redisKey);
            $value = json_encode(array(
                'url' => $data['url'],
                'type' => 'niceUrl',
                'isUgcBackend' => $data['isUgcBackend'],
            ));
            $redis->store($value);
        }
    }

    /**
     * Generates a redis key for a redirect on the an alias entry
     *
     * @param $aliasUrl string
     * @return string|void
     */
    protected function buildKey($aliasUrl = null)
    {
        // first alias will always be written implicitly by base-renderer!
        if (is_null($aliasUrl)) {
            $aliases = $this->getAttributeValue('nice_urls');
            $aliasUrl = $aliases[0]['col_0'];
            $aliasUrl = $this->normalizeAlias($aliasUrl);
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

    public function normalizeAlias($alias)
    {
        return mb_strtolower($alias, 'UTF-8');
    }

    private function getAliasUrls(array &$attributeList)
    {
        $urlList = array();
        foreach ($attributeList as $attribute) {
            $urlList [] = $this->normalizeAlias($attribute['col_0']);
        }
        return $urlList;
    }
}
