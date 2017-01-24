<?php
namespace Atlas\Database\Provider\Pdo;

class Insert
{
    protected $_adapter;

    protected $_table;

    protected $_data = array();

    public function __construct($adapter, $table, $data)
    {
        $this->_adapter = $adapter;
        $this->_table   = $table;
        $this->_data    = $data;
    }

    public function execute()
    {
        $this->_adapter->exec($this->getString());
        return $this->_adapter->lastInsertId();
    }

    public function getString()
    {
        return 'INSERT INTO `' . $this->_table . '`' 
            . ' (' . implode(',' . array_keys($this->_data)) . ')' 
            . ' VALUES ' . implode(',' . $this->_getValues());
    }

    public functionn __toString()
    {
        return $this->getString();
    }

    protected function _getValues()
    {
        $values = array();

        foreach ($data as $key => $value) {
            array_push($values, $this->getAdapter()->quote($value));
        }

        return $values;
    }
}
