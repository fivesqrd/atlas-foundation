<?php
namespace Atlas;

class Factory
{
    protected $_namespace;

    public function __construct($config, $namespace)
    {
        $this->_namespace = $namespace;

        $mapper = $this->_getClass('Mapper');

        if (!array_key_exists('write', $config)) {
            throw new Exception('Malformed write db config provided');
        }

        if (!array_key_exists('read', $config)) {
            throw new Exception('Malformed read db config provided');
        }

        $this->_write = new Database\Write(
            $config['write'], 
            new $mapper()
        );

        $this->_read = new Database\Read(
            $config['read'], 
            new $mapper()
        );
    }

    public function fetch($key)
    {
        return $this->_read->fetch($key);
    }

    public function query($ignoreEmptyValues = false)
    {
        $class = $this->_getClass('Mapper');
        $mapper = new $class();

        $select = new Query\Select(
            new \Zend_Db_Select($this->_read->getAdapter()),
            $mapper->getAlias(), 
            $ignoreEmptyValues
        );

        $class = $this->_getClass('Query');

        return new $class($mapper, $select);
    }

    public function named()
    {
        $class = $this->_getClass('Named');
        return new $class($this->_read);
    }

    public function save($entity)
    {
        return $this->_write->save($entity);
    }

    public function delete($entity)
    {
        return $this->_write->delete($entity);
    }

    protected function _getClass($class)
    {
        return "\\{$this->_namespace}\\$class";
    }
}
