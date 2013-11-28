<?php

abstract class mxdActiveRecord extends mxdDTO
{

    const DEFAULT_ID_COLUMN = 'id';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';

    protected $_database;
    protected $_formattedTableName;
    protected $_idColumn = self::DEFAULT_ID_COLUMN;

    /**
     * internal list of records related to this activerecord
     * the list will be checked during the save method and is able to delete missing related records
     * @var array
     */
    private $_relatedRecords = array();

    /**
     * this is a list of primary ids that will be deleted while saving
     * @var array
     */
    private $_deleteRelatedRecords = array();

    /**
     * constructor
     *
     */
    public function __construct()
    {
    }

    /**
     * return the current table name
     *
     * @return string
     */
    abstract public function getTableName();

    /**
     * returna connection to the databae
     * @return eZDB
     */
    protected function getDatabase()
    {
        if (false === isset($this->_database)) {
            $this->_database = eZDB::instance();
        }
        return $this->_database;
    }

    /**
     * set the connection to the database
     *
     * @param eZDB $database
     * @return void
     */
    public function setDatabase(eZDB $database)
    {
        $this->_database = $database;
    }

    /**
     * return the value of the idColumn
     * this is the primary key
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getData($this->_idColumn);
    }

    /**
     * check if the record was already written to database
     *
     * @return boolean
     */
    public function hasId()
    {
        $hasId = $this->dataIsSet($this->_idColumn);
        return $hasId;
    }

    /**
     * set the name of the primary id column
     *
     * @param string $idColumn
     */
    public function setIdColumn($idColumn)
    {
        $this->_idColumn = $idColumn;
    }

    /**
     * format the table with backticks
     *
     * @return void
     */
    private function getFormattedTableName()
    {
        if (true === isset($this->_formattedTableName)) {
            return $this->_formattedTableName;
        }
        $table = $this->getTableName();

        // remove backticks
        $table = str_replace('`', '', $table);

        // split table name into parts
        $parts = explode('.', $table);

        $formatted = array();
        foreach ($parts as $part) {
            $formatted[] = '`' . $part . '`';
        }
        // concat all parts with backticks
        $this->_formattedTableName = implode('.', $formatted);
        return $this->_formattedTableName;
    }

    /**
     * load the data from the database
     *
     * @param $id
     * @throws Exception
     */
    public function load($id)
    {
        $sql = sprintf('SELECT * FROM %s WHERE `%s` = %d', $this->getFormattedTableName(), $this->_idColumn, $id);
        $row = $this->getDatabase()->arrayQuery($sql);
        if (empty($row)) {
            throw new Exception(sprintf('Failed to load row with primary key "%s" from table "%s"', $id, $this->getFormattedTableName()));
        }
        $firstRow = array_shift($row);
        $this->initFromArray($firstRow);
        $this->_isDirty = false;

    }

    /**
     * save the current data array to the database
     * if the idColumn is set, we update data
     *
     * returns false, if there is nothing to save
     *
     * @return boolean
     */
    public function save()
    {
        $changed = false;
        if ($this->dataIsSet($this->_idColumn) && true === $this->_isDirty) {
            $this->update();
            $changed = true;
        } else if (false === $this->dataIsSet($this->_idColumn)) {
            $this->insert();
            $changed = true;
        }

        $relatedRecordChanged = $this->saveRelatedRecords();
        $changed = $changed || $relatedRecordChanged;

        // reset the change flag
        $this->_isDirty = false;
        return $changed;
    }

    /**
     * save the related records if any
     *
     * @return boolean
     */
    private function saveRelatedRecords()
    {
        $changed = false;
        // save related records if available
        foreach ($this->_relatedRecords as $relatedRecordArr) {
            $relatedRecord = $relatedRecordArr['record'];
            $relatedRecord->setData($relatedRecordArr['foreignField'], $this->getId());
            $relatedRecordChanged = $relatedRecord->save();
            $changed = $changed || $relatedRecordChanged;
        }

        // delete flagged records
        foreach ($this->_deleteRelatedRecords as $relatedRecord) {
            $relatedRecord->delete();
        }
        $this->_deleteRelatedRecords = array();

        return $changed;
    }

    /**
     * insert the dataarray in the database table
     *
     * @param boolean $insertIgnore
     * @return void
     * @throws Exception
     */
    public function insert($insertIgnore = false)
    {
        $values = array();
        $columns = array();

        foreach ($this->_data as $key => $value) {
            $columns[] = '`' . $key . '`';
            if (true === is_null($value)) {
                $values[] = 'null';
            } else {
                $values[] = '"' . $this->getDatabase()->escapeString($value) . '"';
            }
        }

        $sql = sprintf('INSERT %s INTO %s (%s) VALUES (%s)',
            (false !== $insertIgnore) ? 'IGNORE' : '',
            $this->getFormattedTableName(),
            implode(',', $columns),
            implode(',', $values)
        );
        eZDebug::writeDebug(sprintf('%s::%s sql=%s', __CLASS__, __METHOD__, $sql));

        $success = $this->getDatabase()->query($sql);
        if (false === $success) {
            $error = sprintf('MYSQL Error in ActiveRecord: %s [%d]', $this->getDatabase()->errorMessage(), $this->getDatabase()->errorNumber());
            throw new Exception($error);
        }

        // set the new id
        if (false === $this->hasId()) {
            $id = $this->getDatabase()->lastSerialID($this->getFormattedTableName(), $this->_idColumn);
            $this->setData($this->_idColumn, $id);
        }
    }

    /**
     * update  the dataarray in the database table
     *
     * @return void
     * @throws Exception
     */
    public function update()
    {
        $data = array();
        foreach ($this->_data as $key => $value) {
            // we do not update the updated_at field, it will be changed by the database
            if ($key === self::FIELD_UPDATED_AT) {
                continue;
            }

            if ($key != $this->_idColumn && !is_null($value)) {
                $data[] = sprintf('`%s` = "%s"', $key, $this->getDatabase()->escapeString($value));
            } else if ($key != $this->_idColumn && is_null($value)) {
                $data[] = sprintf('`%s` = NULL', $key);
            }
        }

        $sql = sprintf('UPDATE %s SET %s WHERE `%s` = %d',
            $this->getFormattedTableName(),
            implode(',', $data),
            $this->_idColumn,
            $this->getId()
        );
        eZDebug::writeDebug(sprintf('%s::%s sql=%s', __CLASS__, __METHOD__, $sql));

        $success = $this->getDatabase()->query($sql);
        if (false === $success) {
            $error = sprintf('MYSQL Error in ActiveRecord: %s [%d]', $this->getDatabase()->errorMessage(), $this->getDatabase()->errorNumber());
            throw new Exception($error);
        }
    }

    /**
     * remove the data with the given primary key from the database
     *
     * @return integer affected rows
     */
    public function delete()
    {
        // load children, if the internal class variable is empty
        if (empty($this->_relatedRecords)) {
            $this->loadChildren();
        }

        // delete related records from database
        foreach ($this->_relatedRecords as $relatedRecordArr) {
            $relatedRecord = $relatedRecordArr['record'];
            if ($relatedRecord->dataIsSet($this->_idColumn)) {
                $relatedRecord->delete();
            }
        }

        // delete flagged records
        foreach ($this->_deleteRelatedRecords as $relatedRecord) {
            $relatedRecord->delete();
        }

        $success = $this->deleteById($this->getId());
        return $success;
    }

    /**
     * Remove the data with a given id
     *
     * @param integer $id
     * @return boolean
     */
    public function deleteById($id)
    {
        $sql = sprintf('
                DELETE FROM
                    %s
                WHERE
                    `%s` = %d
        ',
            $this->getFormattedTableName(),
            $this->_idColumn,
            $id
        );
        $success = $this->getDatabase()->query($sql);
        $this->_isDirty = true;
        return $success;
    }

    /**
     * get an array with the data from this record
     *
     * @return array
     * @return multitype:
     */
    public function __toArray()
    {
        return $this->_data;
    }

    /**
     * compare the content of the current with the given record
     * meta infos like created_at, updated_at and the idcolumns will not be compared
     *
     * @param mxdActiveRecord $record
     * @return boolean
     */
    public function isEqual(mxdActiveRecord $record)
    {
        foreach ($this->_data as $key => $value) {
            // skip meta infos
            if ($key === self::FIELD_CREATED_AT || $key === self::FIELD_UPDATED_AT) {
                continue;
            }

            // skip id column
            if ($key === $this->_idColumn) {
                continue;
            }

            if ($value != $record->getData($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * load all sub elements
     *
     * @return void
     */
    public function loadChildren()
    {
    }

    /**
     * add a related record to an internal list
     *
     * @param string $key
     * @param mxdActiveRecord $relatedRecord
     * @param string $foreignFieldName
     */
    public function addRelatedRecord($key, mxdActiveRecord $relatedRecord, $foreignFieldName)
    {
        $this->_relatedRecords[$key] = array(
            'record' => $relatedRecord,
            'foreignField' => $foreignFieldName
        );
    }

    /**
     * get a list of all related records
     *
     * @return array
     */
    public function getRelatedRecords()
    {
        $records = array();
        foreach ($this->_relatedRecords as $key => $arr) {
            $records[$key] = $arr['record'];
        }
        return $records;
    }

    /**
     * return the related record with the given key
     *
     * @param string $key
     * @return mxdActiveRecord
     */
    public function getRelatedRecord($key)
    {
        if (false === isset($this->_relatedRecords[$key])) {
            return false;
        }

        return $this->_relatedRecords[$key]['record'];
    }

    /**
     * check if a record with the given key exists
     *
     * @param string $key
     * @return boolean
     */
    public function hasRelatedRecord($key)
    {
        return isset($this->_relatedRecords[$key]);
    }

    /**
     * remove the relatedRecord from the list
     * and add it to the removelist to be able to delete it during save method
     *
     * @param string $key
     */
    public function removeRelatedRecord($key)
    {
        $relatedRecord = $this->getRelatedRecord($key);
        if ($relatedRecord->hasId()) {
            $this->_deleteRelatedRecords[] = $relatedRecord;
        }
        unset($this->_relatedRecords[$key]);
    }

    /**
     * deep load of all related records
     *
     * @return void
     */
    public function loadAll()
    {
        // check if the records were already loaded
        if (false === empty($this->_relatedRecords)) {
            return;
        }

        $this->loadChildren();
        foreach ($this->getRelatedRecords() as $record) {
            $record->loadAll();
        }
    }

}
