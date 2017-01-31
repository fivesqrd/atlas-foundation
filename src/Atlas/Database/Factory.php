<?php
namespace Atlas\Database;

class Factory
{
    protected $_config;

    protected $_namespace;

    public function __construct($config, $namespace)
    {
        $this->_config = $config;
        $this->_namespace = $namespace;
    }

    public function getClass($class)
    {
        return "\\{$this->_namespace}\\$class";
    }

    public function adapter($mode)
    {
        if (!array_key_exists($mode, $this->_config)) {
            throw new Exception('Malformed write db config provided');
        }

        return new \Zend_Db_Adapter_Pdo_Mysql(
            $this->_config[$mode]
        );
    }

    public function mapper()
    {
        $class = $this->_getClass('Mapper');
        return new $class();
    }

    public function select($ignoreEmptyValues = false)
    {
        return new Select(
            new \Zend_Db_Select($this->adapter('read')),
            $this->mapper()->getAlias(), 
            $ignoreEmptyValues
        );
    }

    public function write()
    {
        return new Write(
            $this->adapter('write'), $this->mapper()
        );
    }
}
