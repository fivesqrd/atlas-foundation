<?php
namespace Atlas;

class Proxy
{
    protected $_factory;

    public function __construct($factory)
    {
        $this->_factory = $factory;
    }

    public function fetch($key)
    {
        return $this->_factory->fetch($key)->one();
    }

    public function query($ignoreEmptyValues = false)
    {
        return $this->_factory->query($ignoreEmptyValues);
    }

    public function named()
    {
        return new $this->_factory->named();
    }

    public function save($entity)
    {
        return $this->_factory->write()->save($entity);
    }

    public function delete($entity)
    {
        return $this->_factory->write()->delete($entity);
    }
}
