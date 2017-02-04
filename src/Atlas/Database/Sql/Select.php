<?php
namespace Atlas\Database\Sql;

class Select
{
    protected $_alias;

    protected $_joins;

    public function __construct()
    {
        $this->_where = new Sql\Where(); 
    }

    public function assemble($what, $where)
    {
        return "SELECT {$what} FROM {$where}";
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

        return false;
    }
}
