<?php
namespace Atlas\Database\Sql;

class Update
{
    protected $_table;

    protected $_where;

    protected $_data = array();

    public function __construct($table, $data)
    {
        $this->_table = $table;
        $this->_where = new Sql\Where(); 
    }

    public function assemble()
    {
        return "UPDATE {$this->_table}" 
            . ' SET ' . $this->_getPlaceholders()
            . $this->where()->assemble();
    }

    public function getBoundValues()
    {
        return array_values($this->_data);
    }

    protected function _getPlaceholders()
    {
        $keys = array();

        foreach (array_keys($this->_data) as $key) {
            array_push($keys, "{$key} = ?");
        }

        return implode(', ', $keys;
    }

    public function where()
    {
        return $this->_where;
    }
}
