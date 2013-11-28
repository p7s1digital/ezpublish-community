<?php

class myvideoMetaData extends eZPersistentObject
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
                'title' => array(
                    'name' => 'title',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'keywords' => array(
                    'name' => 'keywords',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'description' => array(
                    'name' => 'description',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'robots' => array(
                    'name' => 'robots',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
                'image_url' => array(
                    'name' => 'image_url',
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
            'class_name' => 'myvideoMetaData',
            'name' => 'myvideometadata'
        );
    }
}
