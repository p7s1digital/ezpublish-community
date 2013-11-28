<?php

class myvideoSimAdConfig extends eZPersistentObject
{
    /**
     * @param $row
     */
    public function __construct($row)
    {
        parent::__construct($row);
    }

    /**
     * @param $contentObjectID
     * @return array|eZPersistentObject|null
     */
    public static function fetchByContentObjectID($contentObjectID)
    {
        return eZPersistentObject::fetchObject(self::definition(), null, array(
            'contentobject_id' => $contentObjectID
        ));
    }

    /**
     * @return array
     */
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
                'channel' => array(
                    'name' => 'channel',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'subchannel1' => array(
                    'name' => 'subchannel1',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'subchannel2' => array(
                    'name' => 'subchannel2',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'dfpsite' => array(
                    'name' => 'dfpsite',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'dfpzone' => array(
                    'name' => 'dfpzone',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                )
            ),
            'keys' => array(
                'contentobject_id'
            ),
            'function_attributes' => array(),
            'sort' => array(
                'contentobject_id' => 'asc'
            ),
            'class_name' => 'myvideoSimAdConfig',
            'name' => 'myvideosimadconfig'
        );
    }
}
