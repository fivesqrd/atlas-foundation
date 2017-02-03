<?php
namespace Atlas\Database;

class Select
{
    protected $_sql;

    protected $_alias;

    protected $_joins;

    protected $_ignoreEmptyValues;

    public function __construct($sql, $alias, $ignoreEmptyValues = false)
    {
        $this->_sql = $sql;
        $this->_alias = $alias;
        $this->_ignoreEmptyValues = $ignoreEmptyValues;
    }

    public function toString()
    {
        return $this->assemble();
    }

    public function assemble()
    {
    }

    public function join($table, $alias = null)
    {
        if ($this->isJoined($alias)) {
            return $this->getJoin($alias);
        }

        $join = new Select\Join($table, $alias);
        $this->_joins[$join->getAlias()] = $join;
        return $join; 
    }

    public function where()
    {
        return $this->_where;
    }

    public function isJoined($alias)
    {
        if (array_key_exists($alias, $this->_joins)) {
            return true;
        } 

        if ($alias == $this->_alias) {
            return true;
        }

        return false;
    }
}
