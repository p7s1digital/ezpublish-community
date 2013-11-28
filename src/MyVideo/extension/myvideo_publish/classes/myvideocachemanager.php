<?php
/**
 * Description of MyVideoCacheManager
 */
class MyVideoCacheManager
{
    /**
     * name of the logfile
     */
    const LOGFILE_NAME = 'myvideo_cache_expire.log';
    /**
     * if an object is already processed this value is stored instead in the queue
     */
    const STATE_IS_PROCESSED = true;
    /**
     * Key/Value-List (array) that holds the queued objects.
     * Object id is used as key. The queued object as value.
     * If an object is already processed value is const STATE_IS_PROCESSED instead.
     *
     * @var array $itsQueue
     */
    private $itsQueue = array();
    /**
     * List that contains all objects that have to be expired/rendered again.
     *
     * @var array $itsExpireList
     */
    private $itsExpireList = array();
    /**
     * The object which initiated the cache expire process.
     * This object has already been stored so it doesn't need to be rendered again.
     *
     * @var eZContentObject $triggeredObject
     */
    private $triggeredObject = null;

    public function __construct()
    {
    }

    /**
     * Expires objects.
     * That means the objects are rendered again and the result is stored in redis.
     */
    public static function expire(eZContentObject $obj)
    {
        self::logObject($obj, 'TRIGGERED EXPIRE');
        $manager = self::getInstance();
        $manager->triggeredObject = $obj;
        $manager->addToQueue($obj);
        $manager->processQueue();
    }

    public function getQueueIds()
    {
        return $this->itsQueue;
    }

    /**
     * @param array $itsExpireList
     */
    public function setItsExpireList($itsExpireList)
    {
        $this->itsExpireList = $itsExpireList;
    }

    /**
     * @return array
     */
    public function getItsExpireList()
    {
        return $this->itsExpireList;
    }

    /**
     * @param array $itsQueue
     */
    public function setItsQueue($itsQueue)
    {
        $this->itsQueue = $itsQueue;
    }

    /**
     * @return array
     */
    public function getItsQueue()
    {
        return $this->itsQueue;
    }

    /**
     * @param \eZContentObject $triggeredObject
     */
    public function setTriggeredObject($triggeredObject)
    {
        $this->triggeredObject = $triggeredObject;
    }

    /**
     * @return \eZContentObject
     */
    public function getTriggeredObject()
    {
        return $this->triggeredObject;
    }

    /**
     * helper method to write information about an object to log file
     *
     * @param eZContentObject $object
     * @param String $msg
     */
    private static function logObject($object, $msg = 'This')
    {
        self::log(sprintf('%s OBJECT #%d [%s] "%s"', $msg, $object->ID, $object->ClassIdentifier, $object->Name));
    }

    /**
     * helper method to write a log file
     *
     * @param String $msg
     */
    private static function log($msg)
    {
        eZLog::write($msg, self::LOGFILE_NAME, 'var/log');
    }

    /**
     * Returns an instance
     *
     * @return \MyVideoCacheManager
     */
    private static function getInstance()
    {
        return new MyVideoCacheManager();
    }

    /**
     * adds an eZContentObject to the queue
     *
     * @param eZContentObject $obj
     * @return boolean
     */
    public function addToQueue($obj)
    {
        $key = $obj->ID;
        if (array_key_exists($key, $this->itsQueue)) {
            self::logObject($obj, 'ALREADY IN QUEUE - SKIP ');
            return false;
        }
        $this->itsQueue[$key] = $obj;
        return true;
    }

    /**
     * Processes the queue.
     * During processing new objects might be added to the queue.
     * Finally the expiration of the object list gets called.
     */
    public function processQueue()
    {
        while ($this->hasPendingObjectsInQueue()) {
            $this->processPendingObject();
        }

        $this->expireObjectList();
        self::log("DONE");
    }

    /**
     * Checks if the queue of pending objects still contains an object
     *
     * @return boolean
     */
    public function hasPendingObjectsInQueue()
    {
        $tmp = $this->itsQueue;
        foreach ($tmp as $item) {
            if (is_object($item)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Processes a content object
     */
    public function processPendingObject()
    {
        /** @var eZContentObject $contentObject */
        $contentObject = $this->shiftPendingObjectFromQueue();
        /** @var array $settings */
        $settings = $this->getSettings($contentObject);
        // get related Objects
        self::logObject($contentObject, 'INSPECT');

        if (!empty($settings['ClearRelationAttribute'])) {
            $this->processRelationAttribute($contentObject);
        }

        if (!empty($settings['ClearRelationAttributeReverse'])) {
            $this->processRelationAttributeReverse($contentObject, $settings['ClearRelationAttributeReverse']);
        }

        if (!empty($settings['ClearRelationCommon'])) {
            $this->processRelationCommon($contentObject);
        }

        if (!empty($settings['ClearRelationCommonReverse'])) {
            $this->processRelationCommonReverse($contentObject);
        }

        if (!empty($settings['ClearChildren'])) {
            $this->processChildren($contentObject);
        }

        if (!empty($settings['ClearParents'])) {
            $this->processParents($contentObject, $settings['ClearParents']);
        }

        $this->itsExpireList[] = $contentObject;
    }

    /**
     * Get an object from the queue or false if there is no object
     *
     * @return eZContentObject|boolean
     */
    private function shiftPendingObjectFromQueue()
    {
        foreach ($this->itsQueue as $key => $obj) {
            if (is_object($obj)) {
                $this->itsQueue[$key] = self::STATE_IS_PROCESSED;
                return $obj;
            }
        }

        return false;
    }

    /**
     * Reads settings from ini-file
     * to determine which relations for which content types have to be respected
     *
     * @param eZContentObject $contentObject
     * @return array
     */
    private function getSettings($contentObject)
    {
        $default = array('ClearRelationAttribute' => array(),
            'ClearRelationAttributeReverse' => array(),
            'ClearRelationCommon' => array(),
            'ClearRelationCommonReverse' => array(),
            'ClearChildren' => array(),
            'ClearParents' => array(),
        );
        $classIdentifier = $contentObject->ClassIdentifier;
        $ini = eZINI::instance('myvideo_publish_cache.ini');
        $iniSettings = $ini->hasGroup($classIdentifier) ? $ini->group($classIdentifier) : array();
        $settings = array_merge($default, $iniSettings);

        return $settings;
    }

    /**
     * Checks Attribute Relations
     *
     * @param eZContentObject $contentObject
     * @return boolean
     */
    private function processRelationAttribute($contentObject)
    {
        self::log(__METHOD__);
        $params = array('AllRelations' => true);
        $n = $contentObject->relatedObjectCount(false, 0, false, $params);

        if ($n == 0) {
            self::log('has no attribute relations - nothing to do ');
            return false;
        } else {
            self::log('has attribute relations - something to do ');
            $objectList = $contentObject->relatedObjects(false, false, 0, false, $params);
            foreach ($objectList as $object) {
                self::logObject($object, 'QUEUE');
                $this->addToQueue($object);
            }
        }
    }

    /**
     * Checks reverse attribute relations
     *
     * @param eZContentObject $contentObject
     * @param array $validClasses
     * @return bool
     */
    private function processRelationAttributeReverse($contentObject, $validClasses = array())
    {
        self::log(__METHOD__);
        $params = array('AllRelations' => eZContentObject::RELATION_ATTRIBUTE);
        $n = $contentObject->reverseRelatedObjectCount(null, null, $params);
        if ($n == 0) {
            self::log('has no attribute relations - nothing to do ');
            return false;
        } else {
            self::log('has attribute relations - something to do ');
            /** @var array $objectList */
            $objectList = $contentObject->reverseRelatedObjectList(null, null, null, $params);
            foreach ($objectList as $object) {
                if (in_array($object->ClassIdentifier, $validClasses)) {
                    self::logObject($object, 'QUEUE');
                    $this->addToQueue($object);
                } else {
                    self::logObject($object, 'SKIP');
                }
            }
        }
    }

    /**
     * Checks Common Relations
     *
     * @param eZContentObject $contentObject
     * @return boolean
     */
    private function processRelationCommon($contentObject)
    {
        self::log(__METHOD__);
        self::log('not implemented yet');
    }

    /**
     * Checks Reverse Common Relations
     *
     * @param eZContentObject $contentObject
     * @return boolean
     */
    private function processRelationCommonReverse($contentObject)
    {
        self::log(__METHOD__);
        self::log('not implemented yet');
    }

    /**
     * Checks child objects
     *
     * @param eZContentObject $contentObject
     */
    private function processChildren($contentObject)
    {
        self::log(__METHOD__);
        self::log('not implemented yet');
    }

    /**
     * Checks parent objects
     *
     * @param eZContentObject $contentObject
     * @param $validClasses
     */
    private function processParents($contentObject, $validClasses)
    {
        self::log(__METHOD__);

        $assignedNodes = $contentObject->assignedNodes();
        foreach ($assignedNodes as $aNode) {
            $parentNodeList = eZContentObjectTreeNode::fetchClassIdentifierListByPathString($aNode->PathString, false);
            foreach (array_reverse($parentNodeList) as $aParentNode) {
                $node = eZContentObjectTreeNode::fetch($aParentNode['node_id']);
                /** @var ezContentObject $object */
                $object = $node->object();
                if (in_array($aParentNode['class_identifier'], $validClasses)) {
                    self::logObject($object, 'QUEUE');
                    $this->addToQueue($object);
                } else {
                    self::logObject($object, 'SKIP');
                }
            }
        }
    }

    /**
     * Process list of objects that should expire.
     */
    private function expireObjectList()
    {
        self::log('NOW EXPIRE QUEUED OBJECTS');
        foreach ($this->itsExpireList as $obj) {
            if ($obj == $this->triggeredObject) {
                self::logObject($obj, 'SKIP INITIALLY TRIGGERED');
                continue;
            }
            self::logObject($obj, 'EXPIRE');
            MyVideoRenderer::render($obj);
        }
    }
}
