<?php

class xrowworkflow extends eZPersistentObject
{
    const STATE_GROUP = 'publishstate';
    const ONLINE = 'live';
    const OFFLINE = 'expired';
    const QUEUE = 'pending';

    public function __construct($row)
    {
        parent::__construct($row);
    }

    public static function fetchByContentObjectID($contentObjectID)
    {
        return eZPersistentObject::fetchObject(xrowworkflow::definition(), null, array(
            'contentobject_id' => $contentObjectID
        ));
    }

    public static function definition()
    {
        return array(
            'fields' => array(
                'contentobject_id' => array(
                    'name' => 'contentobject_id',
                    'datatype' => 'integer',
                    'default' => null,
                    'required' => true
                ),
                'publish' => array(
                    'name' => 'publish',
                    'datatype' => 'integer',
                    'default' => 0,
                    'required' => true
                ),
                'expire' => array(
                    'name' => 'expire',
                    'datatype' => 'integer',
                    'default' => 0,
                    'required' => true
                )
            ),
            'keys' => array(
                'contentobject_id'
            ),
            'function_attributes' => array(),
            'sort' => array(
                'contentobject_id' => 'asc'
            ),
            'class_name' => 'xrowworkflow',
            'name' => 'xrowworkflow'
        );
    }

    public function check()
    {
        if (isset($this->expire) and $this->expire < time() and $this->expire > 0) {
            return $this->offline();
        }
        if (isset($this->publish) and $this->publish < time() and $this->publish > 0) {
            return $this->online();
        }
        if (isset($this->publish) and $this->publish > time()) {
            return $this->queue();
        }
    }

    public function offline()
    {
        $db = eZDB::instance();
        $db->begin();
        self::updateObjectState($this->contentobject_id, array(
            eZContentObjectState::fetchByIdentifier(xrowworkflow::OFFLINE, eZContentObjectStateGroup::fetchByIdentifier(xrowworkflow::STATE_GROUP)->ID)->ID
        ));
        $this->remove();

        // Remove from the flow
        if ($this->contentobject_id > 0) {
            $rows = $db->arrayQuery('SELECT DISTINCT ezm_block.node_id FROM ezm_pool, ezm_block WHERE object_id = ' . (int)$this->contentobject_id . ' AND ezm_pool.block_id = ezm_block.id');
            $db->query('DELETE FROM ezm_pool WHERE object_id = ' . (int)$this->contentobject_id);
        }
        $db->commit();
        $contentObject = null;
        if (isset($rows) && count($rows)) {
            foreach ($rows as $row) {
                $contentObject = eZContentObject::fetchByNodeID($row['node_id']);
                if ($contentObject)
                    eZContentCacheManager::clearContentCache($contentObject->attribute('id'));
            }
        }
        eZContentCacheManager::clearContentCache($this->contentobject_id);
        eZDebug::writeDebug(__METHOD__);
    }

    /**
     * Update a contentobject's state
     *
     * @param int $objectID
     * @param int $selectedStateIDList
     *
     * @return array An array with operation status, always true
     */
    static public function updateObjectState($objectID, $selectedStateIDList)
    {
        $object = eZContentObject::fetch($objectID);

        // we don't need to re-assign states the object currently already has assigned
        $currentStateIDArray = $object->attribute('state_id_array');
        $selectedStateIDList = array_diff($selectedStateIDList, $currentStateIDArray);

        foreach ($selectedStateIDList as $selectedStateID) {
            $state = eZContentObjectState::fetchById($selectedStateID);
            $object->assignState($state);
        }

        //call appropriate method from search engine
        eZSearch::updateObjectState($objectID, $selectedStateIDList);
        eZContentCacheManager::clearContentCacheIfNeeded($objectID);

        //fire the trigger manually
        $triggerName = 'post_updateobjectstate';
        $moduleName = 'content';
        $operationName = 'updateobjectstate';
        $operationParameters = array('object_id' => $objectID, 'state_id_list' => $selectedStateIDList);
        $triggerKeys = array('object_id', 'state_id_list');
        eZTrigger::runTrigger($triggerName, $moduleName, $operationName, $operationParameters, $triggerKeys);
    }

    public function online()
    {
        $object = eZContentObject::fetch($this->contentobject_id);
        $object->setAttribute('published', $this->attribute('publish'));
        $object->store();
        self::updateObjectState($this->contentobject_id, array(
            eZContentObjectState::fetchByIdentifier(self::ONLINE, eZContentObjectStateGroup::fetchByIdentifier(self::STATE_GROUP)->ID)->ID
        ));

        $this->setAttribute('publish', 0);
        $this->store();
        eZContentCacheManager::clearContentCache($this->contentobject_id);
        MyVideoRenderer::render($object);
        eZDebug::writeDebug(__METHOD__);
    }

    public function queue()
    {
        eZDebug::writeDebug(__METHOD__);
        self::updateObjectState($this->contentobject_id, array(
            eZContentObjectState::fetchByIdentifier(self::QUEUE, eZContentObjectStateGroup::fetchByIdentifier(self::STATE_GROUP)->ID)->ID
        ));
        eZDebug::writeDebug(__METHOD__);
    }
}
