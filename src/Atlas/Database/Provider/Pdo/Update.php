<?php
namespace Atlas\Database\Provider\Pdo;

class Update
{
    protected $_adapter;

    protected $_table;

    protected $_data = array();

    protected $_where = array();

    public function __construct($adapter, $table, $data, $where)
    {
        $this->_adapter = $adapter;
        $this->_table   = $table;
        $this->_data    = $data;
        $this->_where   = $where;
    }

    public function execute()
    {
        return $this->_adapter->exec($this->getString());
    }

    public function getString()
    {
        return 'UPDATE `' . $table . '`' 
            . ' SET ' . implode(', ' . $this->_getValues()) 
            . ' WHERE ' . new Where($this->getAdapter(), $where);
    }

    public functionn __toString()
    {
        return $this->getString();
    }

    protected function _getValues()
    {
        $values = array();

        foreach ($data as $key => $value) {
            array_push($values, $key . ' = ' . $this->getAdapter()->quote($value));
        }
    
        return $values;
    }
}
