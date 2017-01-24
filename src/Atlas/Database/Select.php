<?php
namespace Atlas\Database;

class Select
{
    protected $_provider;

    protected $_where;

    protected $_ignoreEmptyValues = false;

    public function __construct($ignoreEmptyValues = false)
    {
        $this->_where = new Select\Where($ignoreEmptyValues);
    }

    public function distinct()
    {
    }

    public function where()
    {
        return $this->_where;
    }

    public function join()
    {
    }

    public function from()
    {
    }

    public function order()
    {
    }

    public function limit()
    {
    }

    public function offset()
    {
    }

    public function fetch($provider, $mapper)
    {
        return new Select\Fetch($provider, $mapper, $this);
    }

    public function sql()
    {
        return new Select\Sql($this);
    }
}
