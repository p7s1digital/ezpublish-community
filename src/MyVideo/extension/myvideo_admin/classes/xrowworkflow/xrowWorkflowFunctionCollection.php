<?php

class xrowWorkflowFunctionCollection
{
    public function byObjectID($id)
    {
        $data = eZPersistentObject::fetchObject(xrowworkflow::definition(),
            null,
            array('contentobject_id' => $id)
        );
        return array('result' => $data);
    }
}
