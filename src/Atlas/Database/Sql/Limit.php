<?php
namespace Atlas\Database\Sql;

class Limit
{
    protected $_limit;

    protected $_offset;

    public function set($limit, $offset = null)
    {
        $this->_limit = $limit;
        $this->_offset = $offset;
    }

    public function assemble()
    {
        $offset = null;

        if ($this->_limit === null) {
            return null;
        }

        if ($this->_offset !== null) {
            $offset = '?,';
        }

        return ' LIMIT ' . $offset . '?';
    }

    public function getBoundValues()
    {
        $values = array();

        if ($this->_offset !== null) {
            array_push($values, (int) $this->_offset);
        }

        if ($this->_limit !== null) {
            array_push($values, (int) $this->_limit);
        }

        return $values;
    }
    
}
