<?php

class xrowworkflowFilter
{
    public static function publish($params)
    {
        $result = array('tables' => false,
            'columns' => false,
            'joins' => false);

        $db = eZDB::instance();
        $joins = array();
        $tables = array('xrowworkflow');
        $result['tables'] = ',' . implode(',', $tables);
        $result['joins'] = ' xrowworkflow.contentobject_id = ezcontentobject.id AND xrowworkflow.publish < ' . time() . ' AND publish > 0 AND ';
        return $result;
    }

    public static function expire($params)
    {
        $result = array('tables' => false,
            'columns' => false,
            'joins' => false);

        $db = eZDB::instance();
        $joins = array();
        $tables = array('xrowworkflow');
        $result['tables'] = ',' . implode(',', $tables);
        $result['joins'] = ' xrowworkflow.contentobject_id = ezcontentobject.id AND xrowworkflow.expire < ' . time() . ' AND expire > 0 AND ';
        return $result;
    }
}
