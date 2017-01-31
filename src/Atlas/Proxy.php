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
        $fetch = new Database\Fetch(
            $this->_factory->adapter(), 
            $this->_factory->mapper(), 
            $this->_factory->select()->isEqual('id', $key)
        );
        
        return $fetch->one();
    }

    public function query($ignoreEmptyValues = false)
    {
        $query = $this->_factory->getClass('Query');

        return new $query(
            $this->_factory->adapter(),
            $this->_factory->mapper(),
            $this->_factory->select()
        );
    }

    public function named()
    {
        $class = $this->_factory->getClass('Named');

        return new $class(
            $this->_factory->adapter(),
            $this->_factory->mapper(),
            $this->_factory->select()
        );
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
