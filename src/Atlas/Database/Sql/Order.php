<?php
namespace Atlas\Database\Sql;

class Order
{
    protected $_spec;

    public function set($spec)
    {
        $this->_spec = $spec;
    }

    public function assemble()
    {
        if ($this->_spec === null) {
            return null;
        }

        return ' ORDER BY ' . $this->_spec;
    }
}
