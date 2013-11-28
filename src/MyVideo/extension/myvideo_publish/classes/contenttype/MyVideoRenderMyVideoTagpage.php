<?php
/**
 * Render an eZPublish contentType myvideo_tagpage
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MyVideoRenderMyVideoTagpage extends MyVideoRendererBase implements MyVideoRendererInterface
{
    const KEY_PART_TAG = 'tag';
    const KEY_PART_CONTENT = 'content';
    const KEY_PART_KEYWORD = 'keyword';
    const KEY_PART_NEWEST_TAGS = 'newest-tags';
    const KEY_PART_IMPORTANT_TAGS = 'important-tags';
    const KEY_PART_RELATED_TAGS = 'related-tags';

    const DEFAULT_CHANNEL = 'Misc';

    static $channelMap = array(
        'Auto & Motor' => 'Auto_&_Motor',
        'TV & Film' => 'TV_&_Film',
        'Infotainment' => 'Infotainment',
        'Sport' => 'Sport',
        'Comedy' => 'Comedy',
        'Webisodes' => 'Webisodes',
        'Talente' => 'Talente',
        'Lifestyle' => 'Lifestyle',
        'Sexy Videos' => 'Sexy'
    );

    /**
     * Creates an array with all information of a tag page
     *
     * @return array
     */
    public function render()
    {
        $data = array();
        $data['type'] = 'tagpage';

        $data['tagName'] = $this->getAttributeValue('title');
        $data['urlPart'] = MyVideoUtils::makeUrlSafe($this->getAttributeValue('url_part'));
        $data['tracking'] = $this->renderMyVideoTrackingData();
        $data['metaData'] = $this->generateMetadata();
        $data['simAdConfig'] = $this->renderMyVideoSimAdConfig();
        // as we have a comma seperated list in this fields, we need to split their values
        // as the frontend expects arrays
        $tags = $this->getAttributeValue('tags');
        if (strlen($tags) > 0) {
            $data['tags'] = $this->processCommaSeperatedList('tags');
        } else {
            $data['tags'] = null;
        }
        $keywords = $this->processCommaSeperatedList('keywords');
        if (empty($keywords) === false) {
            $data['keywords'] = mb_strtolower(implode(",", $keywords));
        } else {
            $data['keywords'] = null;
        }
        $searchTags = $this->processCommaSeperatedList('search_tags');
        if (empty($searchTags) === false) {
            $data['searchTags'] = implode(",", $searchTags);
        } else {
            $data['searchTags'] = null;
        }

        $this->copyFieldContent('editors_text', $data);
        $data['orderBy'] = $this->renderOrderBy();
        $this->copyFieldContent('rss_url', $data);
        $this->copyFieldContent('hashtag', $data);
        $this->copyFieldContent('is_important', $data);
        $this->copyFieldContent('related_tags', $data);
        $surfTips = $this->getAttributeValue('surf_tips');
        $data['surfTips'] = MyVideoUtils::arrayToCamelCase($surfTips);
        $this->copyFieldContent('video', $data);
        $this->copyFieldContent('twitter_widget_id', $data);
        $this->copyFieldContent('social_widget', $data);
        $data['socialWidget'] = $this->createSocialWidgetArray($data);
        $data['solrBoost'] = $this->renderSolrBoost();

        return $data;
    }

    /**
     * @see MyVideoRendererInterface::handleUpdate
     */
    public function handleUpdate($prePublishData)
    {
        /** @var eZContentObject $contentObject */
        $contentObject = $this->ContentObject;

        // when was the tag page updated? We need this for keywords and for newest-tags
        /** @noinspection PhpUndefinedFieldInspection */
        $publishedAt = $contentObject->Modified;
        $redis = $this->getRedisInstance();
        $tagKeyRoot = $this->buildTagKeyRoot();

        $title = $this->getAttributeValue('title');
        $tagName = $this->getAttributeValue('url_part');
        $category = $this->getChannelAreaMapped();

        $keywordList = $this->processCommaSeperatedList('keywords');

        $contentObject->setAttribute('keywords', implode(',', $keywordList));
        $tagList = $this->processCommaSeperatedList('tags');
        $contentObject->setAttribute('tags', implode(',', $tagList));

        // we need the old dataset to check for necessary updates on redis
        $oldContentObject = MyVideoUtils::fetchLastObjectData($contentObject, $this->getLanguage());
        // if there is an older version of the tagpage
        if (null != $oldContentObject) {
            // if the tagname or category has changed, we need to remove the old entries from redis
            if ($oldContentObject['url_part'] != $tagName
                || $oldContentObject['channel_area']['selected_values'][0] != $category
                || $oldContentObject['title'] != $title
            ) {
                // when tagname or category changes, we need to delete the old entries
                $oldUrl = $this->fetchKeyPartIdentifier($oldContentObject);
                // remove the entry from important,newest and related list
                $oldEntry = json_encode(array('title' => $oldContentObject['title'], 'url' => $oldUrl));
                $this->removeTagFromLists($tagKeyRoot, $oldEntry);
                // remove the old content entry
                $oldRedisKey = $this->buildContentKeyRoot() . self::KEY_DELIMITER . $oldUrl;
                $redis->delete($oldRedisKey);
                // and write a new one
                $content = json_encode($this->render());
                $this->storePrimaryRedisData($content);
            }
            // update the keyword list in redis
            $oldKeywordList = explode(',', $oldContentObject['keywords']);
            $newKeywords = array_diff($keywordList, $oldKeywordList);
            $removedKeywords = array_diff($oldKeywordList, $keywordList);
            $this->removeKeywordsFromRedis($removedKeywords, $tagKeyRoot);
            $this->addKeywordsToRedis($newKeywords, $tagKeyRoot, $publishedAt);
        } else {
            $this->addKeywordsToRedis($keywordList, $tagKeyRoot, $publishedAt);
        }

        // add the entry for the new tag name to the related tags set
        $key = $tagKeyRoot . self::KEY_DELIMITER . self::KEY_PART_RELATED_TAGS;
        $redis->hset($key, strtolower($tagName), $this->fetchKeyPartIdentifier());


        // write entry into newest-tags
        $nameUrl = json_encode(array('title' => $title, 'url' => $this->fetchKeyPartIdentifier()));
        $key = $tagKeyRoot . self::KEY_DELIMITER . self::KEY_PART_NEWEST_TAGS;
        $redis->zadd($key, $publishedAt, $nameUrl);

        // write to important tags if this is an important tag
        $isImportant = $this->getAttributeValue('is_important');
        if ($isImportant) {
            $key = $tagKeyRoot . self::KEY_DELIMITER . self::KEY_PART_IMPORTANT_TAGS;
            $redis->zadd($key, $publishedAt, $nameUrl);
        }
    }

    /**
     * @see MyVideoRendererInterface::handleDelete
     */
    public function handleDelete($node)
    {
        $tagKeyRoot = $this->buildTagKeyRoot();
        $title = $this->getAttributeValue('tag_name');
        $urlSafeTagName = $this->fetchKeyPartIdentifier();
        $nameUrl = json_encode(array('title' => $title, 'url' => $urlSafeTagName));

        // remove entries for keywords
        $this->removeKeywordsFromRedis();
        // remove tag from lists
        $this->removeTagFromLists($tagKeyRoot, $nameUrl);
    }

    /**
     * Removes the given entry from the newest, important and related list/hash.
     *
     * @param $tagKeyRoot
     * @param $tagName
     */
    private function removeTagFromLists($tagKeyRoot, $tagName)
    {
        $redis = $this->getRedisInstance();
        // remove entry from newest-tags
        $key = $tagKeyRoot . self::KEY_DELIMITER . self::KEY_PART_NEWEST_TAGS;
        if ($redis->zrank($key, $tagName)) {
            $redis->zrem($key, $tagName);
        }

        // remove from important tags
        $key = $tagKeyRoot . self::KEY_DELIMITER . self::KEY_PART_IMPORTANT_TAGS;
        if ($redis->zrank($key, $tagName)) {
            $redis->zrem($key, $tagName);
        }

        // remove the index from related tags
        $key = $tagKeyRoot . self::KEY_DELIMITER . self::KEY_PART_RELATED_TAGS;
        $redis->hdel($key, $tagName);
    }

    /**
     * Removes keywords for this TagPage from Redis
     */
    protected function removeKeywordsFromRedis()
    {
        $redis = $this->getRedisInstance();
        $tagKeyRoot = $this->buildTagKeyRoot();
        $keywordList = $this->processCommaSeperatedList('keywords');
        if (is_array($keywordList)) {
            foreach ($keywordList as $keyword) {
                $key = $tagKeyRoot . self::KEY_DELIMITER . self::KEY_PART_KEYWORD . self::KEY_DELIMITER .
                    strtolower($keyword);
                $redis->zrem($key, $this->fetchKeyPartIdentifier());
            }
        }
    }

    /**
     * Adds keywords for this TagPage to Redis
     *
     * @param array $keywords       The keywords
     * @param string $tagKeyRoot     The left part of the redis key without the keyword name
     * @param int $modifiedAt     Timestamp to use for redis method ZADD
     */
    protected function addKeywordsToRedis($keywords, $tagKeyRoot, $modifiedAt)
    {
        $redis = $this->getRedisInstance();
        if (is_array($keywords)) {
            foreach ($keywords as $keyword) {
                $key = $tagKeyRoot . self::KEY_DELIMITER . self::KEY_PART_KEYWORD . self::KEY_DELIMITER . strtolower($keyword);
                $redis->zadd($key, $modifiedAt, $this->fetchKeyPartIdentifier());
            }
        }
    }

    /**
     * Builds a redis-key without the identifier
     */
    protected function buildTagKeyRoot()
    {
        $buffer = array();

        $buffer[] = static::KEY_PART_TAG;
        $buffer[] = $this->fetchKeyPartVersion();
        $buffer[] = $this->fetchKeyPartDomain();
        $buffer[] = $this->fetchKeyPartLanguage();
        $buffer[] = $this->fetchKeyPartVisibility();

        $s = implode(self::KEY_DELIMITER, $buffer);
        return $s;
    }

    protected function buildContentKeyRoot()
    {
        $buffer = array();

        $buffer[] = static::KEY_PART_CONTENT;
        $buffer[] = $this->fetchKeyPartVersion();
        $buffer[] = $this->fetchKeyPartDomain();
        $buffer[] = $this->fetchKeyPartLanguage();
        $buffer[] = $this->fetchKeyPartVisibility();

        $s = implode(self::KEY_DELIMITER, $buffer);
        return $s;
    }

    /**
     * Get mapped value of the selected channel ("Bereich")
     *
     * Return the channel ("Bereich") value mapped to a value that matches the url of "Kanäle" / "Themen" on myvideo.de
     *
     * @see $channelMap
     *
     * @return string
     */
    protected function getChannelAreaMapped()
    {
        $topicData = $this->getAttributeValue('channel_area');
        $topic = isset($topicData['selected_values'][0]) ? $topicData['selected_values'][0] : null;

        if (array_key_exists($topic, self::$channelMap)) {
            return self::$channelMap[$topic];
        }

        return self::DEFAULT_CHANNEL;
    }

    /**
     * Generates the last part of the redis key, f.e. '/Themen/MyCategory/tag/MyTag'
     *
     * @param array $contentObject
     * @return string
     */
    protected function fetchKeyPartIdentifier(array $contentObject = null)
    {
        if ($contentObject) {
            $category = self::$channelMap[$contentObject['channel_area']['selected_values'][0]];
            $tagName = $contentObject['url_part'];
        } else {
            $category = $this->getChannelAreaMapped();
            $tagName = $this->getAttributeValue('url_part');
        }
        return '/' . MyVideoRenderer::KEY_PREFIX_TAGPAGE . '/' . MyVideoUtils::makeUrlSafe($category) . '/videos/'
        . MyVideoUtils::makeUrlSafe($tagName);
    }

    /**
     * Renders the value of the order-by-field.
     *
     * Returns one of the following values:
     *  - 'latest' if ordering is set to "Neueste"
     *  - 'most_viewed' if ordering is set to "Meist gesehene"
     *
     * @return string
     */
    private function renderOrderBy()
    {
        $orderBy = $this->getAttributeValue('order_by');

        switch ($orderBy['selected_values'][0]) {
            case 'Neueste':
            default:
                return 'latest';
            case 'Meist gesehene':
                return 'most_viewed';
            case 'Boost Formel':
                return 'boosted';
        }
    }

    /**
     * @return mxdRedis
     */
    private function getRedisInstance()
    {
        return mxdRedis::getInstance();
    }

    /**
     *
     * @param $data Key/Value of the Tagpage Model
     * @return array List of Social Widgets as strings
     */
    private function createSocialWidgetArray($data)
    {

        $result = array();
        if (false == isset($data['socialWidget'])) {
            return $result;
        }
        $text = MyVideosocialwidgettype::getEntry($data['socialWidget']);
        if (strpos($text, 'und') > 0) {
            $result = array('Facebook', 'Twitter');
        } else {
            $result = array($text);
        }
        return $result;
    }

    /**
     * Splits and trims a comma seperated list to an array.
     *
     * @param $fieldName fieldname of the current contentObject
     * @return array An array with the entries of the list
     */
    private function processCommaSeperatedList($fieldName)
    {
        $resultList = array();
        $value = $this->getAttributeValue($fieldName);
        if ($value) {
            $resultList = array_map(
                function ($entry) {
                    return trim($entry);
                },
                explode(',', $value)
            );
        }
        return $resultList;
    }

    /**
     * Render the related solr boost values
     *
     * @return array
     */
    private function renderSolrBoost()
    {
        $buffer = array();
        $data = parent::render();
        // TODO implement helper method
        $arr = $data['fields']['solr_boost']['content']['relation_list'];

        foreach ($arr as $item) {
            $itemNode = eZContentObjectTreeNode::fetch($item['node_id']);

            if ($itemNode->IsInvisible || !$this->isContentObjectOnline($itemNode->ContentObject)) {
                continue;
            }

            $renderer = MyVideoRenderFactory::getInstance($itemNode->ContentObject);
            $buffer[] = $renderer->render();
        }

        return $buffer;
    }

    /**
     * Returns an array with the metatags from db or just the title.
     * @return array
     */
    private function generateMetadata()
    {
        $metadata = $this->renderMyVideoMetaData();
        $title = $this->getAttributeValue('title');
        if (0 == strlen($metadata['title'])) {
            $metadata['title'] = $title;
        }
        if (0 == strlen($metadata['description'])) {
            $metadata['description'] = $title;
        }
        if (0 == strlen($metadata['keywords'])) {
            $metadata['keywords'] = $title;
        }
        return $metadata;
    }
}
