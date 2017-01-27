<?php
namespace Atlas;

class Factory
{
    protected $_config = array();

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function fetch($class, $key)
    {
        return $this->_getReadAdapter($class)
            ->fetch($key);
    }

    public function query($class)
    {
        return $class::query($this->_getReadAdapter($class));
    }

    public function save($entity)
    {
        $class = get_class($entity->factory());
        return $this->_getWriteAdapter($class)
            ->save($entity);
    }

    public function delete($entity)
    {
        return $this->_getWriteAdapter($class)
            ->delete($entity);
    }

    protected function _getWriteAdapter($class)
    {
        return new Atlas\Database\Write(
            $this->_config['write'], 
            $class::mapper()
        );
    }

    protected function _getReadAdapter($class)
    {
        return new Atlas\Database\Read(
            $this->_config['read'], 
            $class::mapper()
        );
    }
}
