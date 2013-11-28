<?php
/**
 * Description of MyVideoRendererBase
 *
 */

class MyVideoRendererBase implements MyVideoRendererInterface
{
    /**
     * Part of redis key.
     * String which is used as delimiter to separate key components.
     */
    const KEY_DELIMITER = ':';
    /**
     * Part of redis key.
     * Key prefix of an identifier part (URL or Remote ID)
     */
    const KEY_PREFIX = '/_partial/';
    /**
     * Part of redis key.
     * Used to distinguish between different types (e.g. with or without route).
     */
    const KEY_PART_TYPE = 'content';
    /**
     * Part of redis key.
     * Version id to allow substantial changes of underlying code
     * without breaking already published objects.
     */
    const KEY_PART_VERSION = 'v1';
    /**
     * Part of redis key.
     * Distinguish between multiple domains not used yet.
     */
    const KEY_PART_DOMAIN = 'de';
    /** @var \eZContentObject|null */
    public $ContentObject = null;
    protected $verbosity = false;
    private $version = null;
    private $language = null;
    private $cachedRenderResult = array();
    private $stateID = null;
    private $visibilityModePreview = null;

    /**
     * @param eZContentObject $contentObject
     * @param int $visibilityModePreview
     */
    public function __construct($contentObject, $visibilityModePreview = MyVideoRenderer::VISIBILITY_MODE_NONE)
    {
        $this->ContentObject = $contentObject;
        $this->visibilityModePreview = $visibilityModePreview;
    }

    /**
     * Check if main node is visible
     *
     * @return bool
     */
    public function isMainNodeVisible()
    {
        $node = $this->ContentObject->attribute('main_node');
        $flagIsVisible = !$node->IsInvisible;

        return $flagIsVisible;
    }

    /**
     * Return the redis storage
     *
     * @return string
     */
    public function fetchKey()
    {
        return $this->buildKey();
    }

    /**
     * @see MyVideoRendererInterface::handleUpdate
     */
    public function handleUpdate($prePublishData)
    {
        // std impl does nothing
    }

    /**
     * @see MyVideoRendererInterface::handleDelete
     */
    public function handleDelete($node)
    {
        // std impl does nothing
    }

    /**
     * Build content type and context specific redis key.
     *
     * Several components (methods) are joined to build the redis storage key.
     * Subclasses can override some or all parts of the key if required.
     *
     * @return string
     */
    protected function buildKey()
    {
        $buffer = array();

        $buffer[] = $this->fetchKeyPartType();
        $buffer[] = $this->fetchKeyPartVersion();
        $buffer[] = $this->fetchKeyPartDomain();
        $buffer[] = $this->fetchKeyPartLanguage();
        $buffer[] = $this->fetchKeyPartVisibility();
        $buffer[] = $this->fetchKeyPartIdentifier();

        $s = implode(self::KEY_DELIMITER, $buffer);
        return $s;
    }

    /**
     * Part of redis key.
     *
     * @return string
     */
    protected function fetchKeyPartType()
    {
        return constant(get_class($this) . '::KEY_PART_TYPE');
    }

    /**
     * Part of redis key.
     *
     * @return string
     */
    protected function fetchKeyPartVersion()
    {
        return constant(get_class($this) . '::KEY_PART_VERSION');
    }

    /**
     * Part of redis key.
     *
     * @return string
     */
    protected function fetchKeyPartDomain()
    {
        return constant(get_class($this) . '::KEY_PART_DOMAIN');
    }

    /**
     * Part of redis key.
     *
     * @return string
     */
    protected function fetchKeyPartLanguage()
    {
        // TODO map language-codes to ezpublish language
        return strtolower(substr($this->getLanguage(), 4));
    }

    /**
     * Part of redis key.
     *
     * @return string
     */
    protected function fetchKeyPartVisibility()
    {
        if (MyVideoRenderer::isPreviewMode()) {
            return 'preview';
        } else {
            return 'live';
        }
    }

    /**
     * Part of redis key.
     *
     * @return string
     */
    protected function fetchKeyPartIdentifier()
    {
        $node = $this->ContentObject->attribute('main_node');
        $language = $this->getLanguage();

        // get the language version of the current node
        /** @var eZContentObjectTreeNode $node */
        $node = eZContentObjectTreeNode::fetch($node->NodeID, $language);
        if (!is_object($node)) {
            return 'FAULT-NO-KEY-FOR-' . $language;
        }

        $url = $node->attribute('url_alias');

        if (empty($url)) {
            $url = $node->attribute('url');
        }
        $url = '/' . ltrim($url, '/'); // add a leading slash

        // TODO implement - this is mock up only
        // has to be identical to the folder name MyVideo
        $url = preg_replace('#^/MyVideo#', '', $url);

        return $url;
    }

    /**
     * Render content object attributes as array
     *
     * @return array|null
     */
    public function render()
    {
        return $this->getAllAttributeValues();
    }

    /**
     * Stores the primary redis data
     *
     * @param $json
     */
    public function storePrimaryRedisData($json)
    {
        $storage = new MyVideoStorageRedis();
        $storage->setKey($this->fetchKey());
        $storage->store($json);
    }

    /**
     * Deletes the primary redis data
     */
    public function deletePrimaryRedisData()
    {
        $storage = new MyVideoStorageRedis();
        $storage->delete($this->fetchKey());
    }

    private function fetchDataMapPreview()
    {
        $obj = $this->getContentObject();

        $versions = $obj->versions();

        $version = null;
        foreach ($versions as $versionObject) {
            if ($versionObject->Status == eZContentObjectVersion::STATUS_DRAFT && $version === null) {
                $version = $versionObject->Version;
            }

            if ($versionObject->Status == eZContentObjectVersion::STATUS_INTERNAL_DRAFT) {
                $version = $versionObject->Version;
                break;
            }

        }

        return $obj->fetchDataMap($version, $this->language);
    }

    private function isSaveAndPreviewAction()
    {
        return isset($GLOBALS['MyVideoPreviewTypeAsGlobal']);
    }

    /**
     * Get from all attributes the values and uses the field renderer classes
     *
     * @return array|null
     */
    protected function getAllAttributeValues()
    {
        if ($this->isSaveAndPreviewAction()) {
            $this->flushCachedRenderResult();
        }

        if ($this->hasCachedRenderResult()) {
            return $this->getCachedRenderResult();
        }

        if ($this->isSaveAndPreviewAction()) {
            $dataMap = $this->fetchDataMapPreview();
        } else {
            $dataMap = $this->getContentObjectDataMap();
        }

        $stack = array();
        $data = $this->getMetaData();

        foreach ($dataMap as $key => $contentObjectAttribute) {
            $fieldRenderer = MyVideoFieldRendererFactory::getInstance($contentObjectAttribute);
            $fieldRenderer->setLanguage($this->getLanguage());
            $result = $fieldRenderer->render();
            // TODO: can we remove the next statement? we should, but does someone rely on isset(...) somewhere?
            if (is_null($result)) {
                continue;
            }
            $stack[$key] = array(
                'name' => $key,
                'type' => $contentObjectAttribute->DataTypeString,
                'content' => $result);
        }

        $data['fields'] = $stack;
        $this->setCachedRenderResult($data);

        return $data;
    }

    /**
     * Check if the render result is available
     *
     * @return bool
     */
    public function hasCachedRenderResult()
    {
        return array_key_exists($this->getLanguage(), $this->cachedRenderResult);
    }

    /**
     * Get the language of the content object
     *
     * @return null
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the language of the content object
     *
     * @param $language
     * @return bool
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return true;
    }

    /**
     * If cache result is available return this otherwise return nothing
     *
     * @return null
     */
    public function getCachedRenderResult()
    {
        if ($this->hasCachedRenderResult()) {
            return $this->cachedRenderResult[$this->getLanguage()];
        } else {
            return null;
        }
    }

    /**
     * Set the result in the cache
     *
     * @param $data
     */
    public function setCachedRenderResult($data)
    {
        $this->cachedRenderResult[$this->getLanguage()] = $data;
    }

    /**
     * Return data map of object respect current version and language
     *
     * @return array
     * @throws Exception
     */
    public function getContentObjectDataMap()
    {
        $obj = $this->getContentObject();
        $dataMap = $obj->fetchDataMap($this->version ? $this->version : $this->ContentObject->CurrentVersion, $this->language);
        return $dataMap;
    }

    /**
     * Return current content object
     *
     * @return eZContentObject
     * @throws Exception
     */
    public function getContentObject()
    {
        $obj = $this->ContentObject;
        if (!$obj instanceof eZContentObject) {
            throw new Exception('Valid content object expected!');
        }
        return $obj;
    }

    /**
     * Set the content object and flush the previous cache
     *
     * @param eZContentObject $contentObject
     * @throws Exception
     */
    public function setContentObject(eZContentObject $contentObject)
    {
        if (!$contentObject instanceof eZContentObject) {
            throw new Exception('Valid content object expected!');
        }
        $this->flushCachedRenderResult();
        $this->ContentObject = $contentObject;
    }

    /**
     * Flush the Cache
     */
    public function flushCachedRenderResult()
    {
        $this->cachedRenderResult = array();
    }

    /**
     * Get the meta data map of the object
     *
     * @return array
     */
    public function getMetaData()
    {
        $data = array();
        $data['type'] = self::beautifyType($this->ContentObject->ClassIdentifier);
        $data['name'] = $this->ContentObject->Name;

        if ($this->verbosity) {
            $data['oid'] = $this->ContentObject->ID;
            $data['version'] = $this->version ? $this->version : $this->ContentObject->CurrentVersion;
            // TODO determine language requested and language responded
            // TODO how to get the main language setting?
            $data['language'] = $this->language ? $this->language : $this->ContentObject->CurrentLanguage;
            $data['language-real'] = $this->ContentObject->CurrentLanguage;
        }

        return $data;
    }

    /**
     * Remove leading myvideo from the identifier
     *
     * @param $type
     * @return mixed
     */
    public static function beautifyType($type)
    {
        return preg_replace('#^myvideo_#', '', $type);

    }

    /**
     * Return the current version
     * @return null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the given version
     *
     * @param $version
     * @return bool
     */
    public function setVersion($version)
    {
        $this->version = intval($version);
        return true;
    }

    /**
     * Enable more meta data of the current object
     *
     * @param $bool
     */
    public function setVerbosity($bool)
    {
        $this->verbosity = (bool)$bool;
    }

    /**
     * Check if the content object has an online status
     *
     * @param eZContentObject $contentObject
     * @return bool
     */
    public function isContentObjectOnline(&$contentObject)
    {
        if (!is_object($contentObject)) {
            return false;
        }

        if (!isset($this->stateID)) {
            $this->stateID = eZContentObjectState::fetchByIdentifier(xrowworkflow::ONLINE, eZContentObjectStateGroup::fetchByIdentifier(xrowworkflow::STATE_GROUP)->ID)->ID;
        }
        $contentObject->stateIDArray(true);

        return in_array($this->stateID, $contentObject->attribute('state_id_array'));
    }

    /**
     * Copy attribute value with destination to the destination array
     *
     * @param $fieldName
     * @param $dst
     * @param bool $toCamelCase
     * @param array $map
     */
    protected function copyFieldContent($fieldName, &$dst, $toCamelCase = true, $map = array())
    {
        $srcContent = $this->getAttributeValue($fieldName, '');
        if ($srcContent !== "") {
            if ($toCamelCase) {
                $fieldName = MyVideoUtils::toCamelCase($fieldName);
            }
            if (is_array($srcContent) && !empty($map)) {
                foreach ($map as $dataKey) {
                    if (array_key_exists($dataKey, $srcContent)) {
                        $dst[$dataKey] = $srcContent[$dataKey];
                    }
                }
            } else {
                $dst[$fieldName] = $srcContent;
            }
        }
    }

    /**
     * Get the whole attribute with the given name.
     *
     * Returns the whole ez-data-structure for the given field or null if the field does not exist
     *
     * @param string $name
     * @return array|null
     */
    public function getAttribute($name)
    {
        $values = $this->getAllAttributeValues();

        return array_key_exists($name, $values['fields']) ? $values['fields'][$name] : null;
    }

    /**
     * Get the value of the the given attribute
     *
     * @param $attributeIdentifier
     * @param null $defaultValue
     * @return null
     */
    public function getAttributeValue($attributeIdentifier, $defaultValue = null)
    {
        $values = $this->getAllAttributeValues();
        if (!array_key_exists($attributeIdentifier, $values['fields'])) {
            return $defaultValue;
        }

        return $values['fields'][$attributeIdentifier]['content'];
    }

    /**
     * Returns the row (defined in myvideoTracking::definition()) of the contentobject_id as object
     *
     * @see myvideoTracking::definition()
     * @return array|eZPersistentObject|null
     */
    private function getMyVideoTrackingData()
    {
        $conditions['contentobject_id'] = $this->ContentObject->ID;

        // if we run the migration we have to handle an psql error if the table not exists
        $db = eZDB::instance();
        $def = myvideoTracking::definition();
        $query = "SELECT COUNT(*) FROM pg_class WHERE relname= '" . $def['name'] . "' AND relkind='r'";
        $row = $db->arrayQuery($query);

        if ($row[0]['count'] === '0') {
            return null;
        } else {
            return eZPersistentObject::fetchObject(myvideoTracking::definition(), null, $conditions);
        }
    }

    /**
     * Returns the row (defined in MyVideoMetadata::definition()) of the contentobject_id as object
     *
     * @see MyVideoMetadata::definition()
     * @return array|eZPersistentObject|null
     */
    private function getMyVideoMetaData()
    {
        $conditions['contentobject_id'] = $this->ContentObject->ID;

        // if we run the migration we have to handle an psql error if the table not exists
        $db = eZDB::instance();
        $def = myvideoMetaData::definition();
        $query = "SELECT COUNT(*) FROM pg_class WHERE relname= '" . $def['name'] . "' AND relkind='r'";
        $row = $db->arrayQuery($query);

        if ($row[0]['count'] === '0') {
            return null;
        } else {
            return eZPersistentObject::fetchObject(myvideoMetaData::definition(), null, $conditions);
        }
    }

    /**
     * Returns the row (defined in myvideoSimAdConfig::definition()) of the contentobject_id as object
     *
     * @see myvideoSimAdConfig::definition()
     * @return array|eZPersistentObject|null
     */
    private function getMyVideoSimAdConfig()
    {
        $conditions['contentobject_id'] = $this->ContentObject->ID;

        // if we run the migration we have to handle an psql error if the table not exists
        $db = eZDB::instance();
        $def = myvideoSimAdConfig::definition();
        $query = "SELECT COUNT(*) FROM pg_class WHERE relname= '" . $def['name'] . "' AND relkind='r'";
        $row = $db->arrayQuery($query);

        if ($row[0]['count'] === '0') {
            return null;
        } else {
            return eZPersistentObject::fetchObject(myvideoSimAdConfig::definition(), null, $conditions);
        }
    }

    /**
     * Render the tracking data in array
     */
    public function renderMyVideoMetaData()
    {
        $data = array();
        $data['type'] = 'metaData';
        $metaData = $this->getMyVideoMetaData();

        if ($metaData !== null and !empty($metaData)) {
            foreach ($metaData as $delta => $value) {
                // skip empty values and values we don't want to store in the redis
                if ($value === false or $value === null or $value === '' or $delta === 'PersistentDataDirty' or $delta === 'contentobject_id') {
                    continue;
                }
                $data[MyVideoUtils::toCamelCase($delta)] = $value;
            }
            return $data;
        } else {
            return array();
        }
    }

    /**
     * Render the tracking data in array
     *
     * @return array
     */
    public function renderMyVideoTrackingData()
    {
        $data = array();
        $data['type'] = 'Tracking';
        $trackingData = $this->getMyVideoTrackingData();

        if ($trackingData !== null and !empty($trackingData)) {
            foreach ($trackingData as $delta => $value) {
                // skip empty values and values we don't want to store in the redis
                if ($value === false or $value === null or $value === '' or $delta === 'PersistentDataDirty' or $delta === 'contentobject_id') {
                    continue;
                }
                $data[MyVideoUtils::toCamelCase($delta)] = $value;
            }
            return $data;
        } else {
            return array();
        }
    }

    /**
     * Render the sim ad configuration in array
     *
     * @return array
     */
    public function renderMyVideoSimAdConfig()
    {
        $data = array();
        $data['type'] = 'SimAdConfig';
        $simAdConfig = $this->getMyVideoSimAdConfig();

        if ($simAdConfig !== null and !empty($simAdConfig)) {
            foreach ($simAdConfig as $delta => $value) {
                // skip empty values and values we don't want to store in the redis
                if ($value === false or $value === null or $value === '' or $delta === 'PersistentDataDirty' or $delta === 'contentobject_id') {
                    continue;
                }
                $data[MyVideoUtils::toCamelCase($delta)] = $value;
            }
            return $data;
        } else {
            return array();
        }
    }

    /**
     * @param array $urls
     * @return array
     */
    protected function flattenTrackingLinks($urls)
    {
        if (!is_array($urls)) {
            return array();
        }

        $ret = array();
        foreach ($urls as $url) {
            if (isset($url['url']) && !empty($url['url']) && strlen($url['url']) > 0) {
                $ret[] = $url['url'];
            }
        }

        return $ret;
    }

    protected function renderMyVideoSushibars()
    {
        $buffer = array();
        $data = $this->getAllAttributeValues();
        // TODO implement herlper method
        $arr = $data['fields']['sushibars']['content']['relation_list'];

        foreach ($arr as $item) {
            /** @var \eZContentObjectTreeNode $itemNode */
            $itemNode = eZContentObjectTreeNode::fetch($item['node_id']);
            if (is_object($itemNode)) {
                /** @var eZContentObject $contentObject */
                $contentObject = $itemNode->object();
            }

            if ($itemNode->IsInvisible || !$this->isContentObjectOnline($contentObject)) {
                continue;
            }

            $renderer = MyVideoRenderFactory::getInstance($contentObject);
            $buffer[] = $renderer->render();
        }

        return $buffer;
    }

    /**
     * Fetches the list of objects by the current attribute key and renders them
     *
     * @param string $attributeKey
     * @param boolean $asSingleItem / if we need just one relation from an single relation not to be an array
     * @return array
     */
    protected function renderAssetRelationList($attributeKey, $asSingleItem = false)
    {
        $data = array();
        $itemList = $this->helperGetNodeIdList($attributeKey);
        foreach ($itemList as $nodeId) {
            /** @var eZContentObjectTreeNode $itemNode */
            $itemNode = eZContentObjectTreeNode::fetch($nodeId);
            if (is_object($itemNode)) {
                /** @var eZContentObject $contentObject */
                $contentObject = $itemNode->object();
            }

            if (!is_object($itemNode) || $itemNode->IsInvisible || !$this->isContentObjectOnline($contentObject)) {
                continue;
            }
            $renderer = MyVideoRenderFactory::getInstance($contentObject);

            if ($asSingleItem) {
                return $renderer->render();
            }

            $data[] = $renderer->render();
        }
        return $data;
    }

    /**
     * Fetches a list of nodes from the (? content tree ?)
     *
     * @param string $identifier
     * @return array
     */
    protected function helperGetNodeIdList($identifier)
    {
        $standard = $this->getAllAttributeValues();
        if (!array_key_exists($identifier, $standard['fields'])) {
            return array();
        }

        $arr = $standard['fields'][$identifier]['content']['relation_list'];
        if (!count($arr)) {
            return array();
        }

        $data = array();
        foreach ($arr as $item) {
            $data[] = $item['node_id'];
        }

        return $data;
    }
}
