<?php
namespace Atlas\Database\Sql;

class Update
{
    protected $_table;

    protected $_where;

    protected $_data = array();

    public function __construct($table, $data, $where)
    {
        $this->_table = $table;
        $this->_data = $data;
        $this->_where = $where;
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

        return implode(', ', $keys);
    }

    public function where()
    {
        return $this->_where;
    }
}
