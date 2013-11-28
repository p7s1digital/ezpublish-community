<?php

class mxdDB extends eZMySQLiDB
{

    /**
     * read the values with the given sql from the database
     * and create an assoc array with the first field of the result as a key
     *
     * i.e. SELECT id, name, description
     * array(4711 => array('name' => 'testname', 'description' => 'testdescription')
     *
     * if the are only 2 field in the result, the array is only a key/value array
     * i.e. SELECT id, name
     * array(4711 => 'testname')
     *
     * @param string $sql
     * @return array
     */
    public function getAssoc($sql)
    {
        $rows = $this->arrayQuery($sql);

        $list = array();
        foreach ($rows as $row) {
            $firstValue = reset($row);

            if (count($row) == 2) {
                $list[$firstValue] = array_pop($row);
            } else {
                $list[$firstValue] = $row;
            }
        }

        return $list;
    }

    /**
     * return a single value from the database
     * i.e.
     *     SELECT COUNT(*) FROM test
     *     SELECT name FROM user WHERE id = 17
     *
     * @param string $sql
     * @return boolean|mixed
     */
    public function getOne($sql)
    {
        $row = $this->getRow($sql);
        if (false === $row) {
            return false;
        }

        $value = array_shift($row);
        return $value;
    }

    /**
     * return a single row from the database
     * i.e.
     *     SELECT * FROM user WHERE id = 17
     *
     * @param string $sql
     * @return array
     */
    public function getRow($sql)
    {
        $rows = $this->arrayQuery($sql);
        if (empty($rows)) {
            return false;
        }

        $row = array_shift($rows);
        return $row;
    }

    /**
     * return an array with the first column of each row
     * i.e.
     *     SELECT name FROM user
     *
     * @param string $sql
     * @return array
     */
    public function getCol($sql)
    {
        $rows = $this->arrayQuery($sql);

        $list = array();
        foreach ($rows as $row) {
            $list[] = reset($row);
        }
        return $list;
    }

}
