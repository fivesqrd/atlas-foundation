<?php
namespace Atlas\Database\Provider\Pdo;

class Where
{
    protected $_adapter;
    
    protected $_values = array();

    public function __construct($adapter, $values)
    {
        $this->_adapter = $adapter;
        $this->_values = $values;
    }

    public function getString()
    {
        $statements = array();

        foreach ($this->_values as $key => $value) {
            array_push($statements, $key . ' = ' . $this->_adapter->quote($value));
        }

        return implode(' AND ', $statements);
    }
    
    public function __toString()
    {
        return $this->getString();
    }
}
