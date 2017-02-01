<?php
namespace Atlas;

class Proxy
{
    protected $_factory;

    protected $_resolver;

    public function __construct($factory, $resolver)
    {
        $this->_factory = $factory;
        $this->_resolver = $resolver;
    }

    public function fetch($key)
    {
        return $this->_factory
            ->fetch($this->_resolver, $key)
            ->one();
    }

    public function query($ignoreEmptyValues = false)
    {
        return $this->_factory->query(
            $this->_resolver, $ignoreEmptyValues
        );
    }

    public function named()
    {
        return $this->_factory->named(
            $this->_resolver
        );
    }

    public function relation($entity)
    {
        return $this->_factory
            ->relation($this->_resolver, $entity);
    }

    public function save($entity)
    {
        return $this->_factory
            ->write($this->_resolver)
            ->save($entity);
    }

    public function save($entity)
    {
        return $this->_factory
            ->write($this->_resolver)
            ->save($entity);
    }

    public function delete($entity)
    {
        return $this->_factory
            ->write($this->_resolver)
            ->delete($entity);
    }
}
