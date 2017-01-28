<?php
namespace Atlas;

class Factory
{
    protected $_class;

    public function __construct($config, $class)
    {
        $this->_write = new Atlas\Database\Write(
            $config['write'], 
            $class\Mapper()
        );

        $this->_read = new Atlas\Database\Write(
            $config['read'], 
            $class\Mapper()
        );

        $this->_class = $class;
    }

    public function fetch($key)
    {
        return $this->_read->fetch($key);
    }

    public function query()
    {
        return $this->_class\Query($this->_read);
    }

    public function named()
    {
        return $this->_class\Named($this->_read);
    }

    public function save($entity)
    {
        return $this->_write->save($entity);
    }

    public function delete($entity)
    {
        return $this->_write->delete($entity);
    }
}
