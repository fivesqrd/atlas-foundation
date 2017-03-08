<?php
namespace Atlas\Database\Sql;

class Delete
{
    protected $_table;

    protected $_where;

    public function __construct($table, $where)
    {
        $this->_table = $table;
        $this->_where = $where;
    }

    public function assemble()
    {
        if (empty($this->_table)) {
            throw new Exception('Table name is required');
        }

        return "DELETE FROM {$this->_table}" . $this->where()->assemble();
    }

    public function getBoundValues()
    {
        return $this->_where->getBoundValues()
    }

    public function where()
    {
        return $this->_where;
    }
}
