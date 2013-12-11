<?php

class myvideoTracking extends eZPersistentObject
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
                'ivw_agofcode' => array(
                    'name' => 'ivw_agofcode',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'ivw_path' => array(
                    'name' => 'ivw_path',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'ga_tvbrand' => array(
                    'name' => 'ga_tvbrand',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'ga_channel' => array(
                    'name' => 'ga_channel',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'ivw_two_category' => array(
                    'name' => 'ivw_two_category',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'ivw_two_channel' => array(
                    'name' => 'ivw_two_channel',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'ivw_two_format' => array(
                    'name' => 'ivw_two_format',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'custom_pixels' => array(
                    'name' => 'custom_pixels',
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
            'class_name' => 'myvideoTracking',
            'name' => 'myvideotracking'
        );
    }
}
