<?php

class myvideoSimAdConfigFunctionCollection
{
    /**
     * @param $id
     * @return array
     */
    public function byObjectID($id)
    {
        $data = eZPersistentObject::fetchObject(myvideoSimAdConfig::definition(),
            null,
            array('contentobject_id' => $id)
        );
        return array('result' => $data);
    }
}
