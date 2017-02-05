<?php
namespace Atlas\Database\Sql;

class Select
{
    protected $_alias;

    protected $_joins = array();

    public function __construct()
    {
        $this->_where = new Where(); 
    }

    public function assemble($what, $where)
    {
        return "SELECT {$what}"
            . " FROM {$where}" 
            . $this->_getJoinString();
    }

    public function join($table, $alias = null)
    {
        if ($this->isJoined($alias)) {
            return $this->getJoin($alias);
        }

        $join = new Join($table, $alias);
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

        return false;
    }
    
    protected function _getJoinString()
    {
        if (empty($this->_joins)) {
            return null;
        }

        $strings = array();

        foreach ($this->_joins as $join) {
            array_push($strings, $join->assemble());
        }

        return ' ' . implode(' ' , $strings);
    }
}
