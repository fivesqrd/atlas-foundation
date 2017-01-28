<?php
namespace Atlas;

abstract class Named
{
    protected $_adapter;

    abstract protected function _factory();

    public function __construct($adapter)
    {
        $this->_adapter = $adapter;
    }
}
