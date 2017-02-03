<?php
namespace Atlas\Database\Select;

class Sql
{
    protected $_joins = array();

    public function __construct($sql, $alias)
    {
        $this->_sql = $sql;
        $this->_alias = $alias;
        $this->_ignoreEmptyValues = $ignoreEmptyValues;
    }

    public function assemble()
    {
    }
    
    /*
    public function record()
    {
        return "SELECT {$this->_disinct}{$this->_select->getAlias()}.* "
            . 'FROM ' . $this->_from
            . 'WHERE ' . $this->_where->getSql();
    }

    public function count()
    {
        return "SELECT COUNT(distinct {$this->_select->getAlias()}.id) "
            . 'FROM ' . $this->_from
            . 'WHERE ' . $this->_where->getSql();
    }

    public function sum($column)
    {
        return "SELECT SUM({$column}) "
            . 'FROM ' . $this->_from
            . 'WHERE ' . $this->_where->getSql();
    }

    public function average($column)
    {
        return "SELECT AVERAGE({$column}) "
            . 'FROM ' . $this->_from
            . 'WHERE ' . $this->_where->getSql();
    }
    */
}
