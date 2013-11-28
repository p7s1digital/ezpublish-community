<?php

class myvideoMetaDataFunctionCollection
{
    /**
     * @param $id
     * @return array
     */
    public function byObjectID($id)
    {
        $data = eZPersistentObject::fetchObject(myvideoMetaData::definition(),
            null,
            array('contentobject_id' => $id)
        );
        return array('result' => $data);
    }
}
