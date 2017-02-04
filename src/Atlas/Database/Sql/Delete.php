<?php
namespace Atlas\Database\Sql;

class Delete
{
    protected $_table;

    protected $_where;

    public function __construct($table)
    {
        $this->_table = $table;
        $this->_where = new Sql\Where(); 
    }

    public function assemble()
    {
        return "DELETE FROM {$this->_table}" . $this->where()->assemble();
    }

    public function getBoundValues()
    {
        return array();
    }

    public function where()
    {
        return $this->_where;
    }
}
