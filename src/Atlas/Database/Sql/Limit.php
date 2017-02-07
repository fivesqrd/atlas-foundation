<?php
namespace Atlas\Database\Sql;

class Limit
{
    protected $_limit;

    protected $_offset;

    public function set($limit, $offset = null)
    {
        $this->_limit = (int) $limit;
        $this->_offset = (int) $offset;
    }

    public function assemble()
    {
        $offset = null;

        if ($this->_limit === null) {
            return null;
        }

        if ($this->_offset !== null) {
            $offset = $this->_offset . ',';
        }

        return ' LIMIT ' . $offset . $this->_limit;
    }
}
