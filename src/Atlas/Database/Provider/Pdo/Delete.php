<?php
namespace Atlas\Database\Provider\Pdo;

class Delete
{
    protected $_adapter;

    protected $_table;

    protected $_where = array();

    public function __construct($adapter, $table, $where)
    {
        $this->_adapter = $adapter;
        $this->_table   = $table;
        $this->_where   = $where;
    }

    public function execute()
    {
        return $this->_adapter->exec($this->getString());
    }

    public function getString()
    {
        return 'DELETE FROM `' . $this->_table . '`' 
            . ' WHERE ' . new Pdo\Where($this->_adapter, $this->_where);
    }

    public functionn __toString()
    {
        return $this->getString();
    }
}
