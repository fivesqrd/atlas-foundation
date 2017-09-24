<?php
namespace Atlas\Database\Sql;

class Insert
{
    protected $_table;

    protected $_where;

    protected $_data = array();

    public function __construct($table, $data)
    {
        $this->_table = $table;
        $this->_data  = $data;
    }

    public function assemble()
    {
        if (empty($this->_table)) {
            throw new Exception('Table name is required');
        }

        return "INSERT INTO {$this->_table}" 
            . " ({$this->_getColumns()})"
            . " VALUES ({$this->_getPlaceholders()})";
    }

    public function getBoundValues()
    {
        return array_values($this->_data);
    }

    protected function _getColumns()
    {
        $columns = array();

        foreach (array_keys($this->_data) as $key) {
            /* todo: support identifiers of other RDMS' as well */
            array_push($columns, "`{$key}`");
        }

        return implode(', ', $columns);
    }

    protected function _getPlaceholders()
    {
        $keys = array();

        foreach (array_keys($this->_data) as $key) {
            array_push($keys, '?');
        }

        return implode(', ', $keys);
    }
}
