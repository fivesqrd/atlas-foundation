<?php
namespace Atlas\Database;

class Resolver
{
    protected $_namespace;

    public function __construct($namespace)
    {
        $this->_namespace = $namespace;
    }

    protected function _getClass($class)
    {
        return "\\{$this->_namespace}\\$class";
    }

    public function mapper()
    {
        $class = $this->_getClass('Mapper');
        return new $class();
    }

    public function query($adapter, $mapper, $select)
    {
        $class = $this->_getClass('Query');
        return new $class($adapter, $mapper, $select);
    }

    public function named()
    {
        $class = $this->_getClass('Named');
        return new $class();
    }
}
