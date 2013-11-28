<?php

class myvideoTrackingFunctionCollection
{
    /**
     * @param $id
     * @return array
     */
    public function byObjectID($id)
    {
        $data = eZPersistentObject::fetchObject(myvideoTracking::definition(),
            null,
            array('contentobject_id' => $id)
        );
        return array('result' => $data);
    }
}
