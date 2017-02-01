<?php
namespace Atlas\Database;

class Factory
{
    protected $_config;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function adapter($mode)
    {
        if (empty($mode)) {
            throw new Exception("Db adapter mode not specified");
        }

        if (!in_array($mode, array('write','read'))) {
            throw new Exception("Invalid db adapter mode '{$mode}' specified");
        }

        if (!array_key_exists($mode, $this->_config)) {
            throw new Exception("Malformed db adapter {$mode} config specified");
        }

        return new \Zend_Db_Adapter_Pdo_Mysql(
            $this->_config[$mode]
        );
    }

    public function select($resolver, $ignoreEmptyValues = false)
    {
        return new Select(
            new \Zend_Db_Select($this->adapter('read')),
            $resolver->mapper()->getAlias(), 
            $ignoreEmptyValues
        );
    }

    public function fetch($resolver, $key)
    {
        $mapper = $resolver->mapper();
        $select = $this->select($mapper->getAlias())
            ->isEqual('id', $key);

        return new Fetch(
            $this->adapter('read'), $mapper, $select
        );
    }

    public function relation($resolver, $entity)
    {
        if (is_numeric($entity)) {
            $entity = $this->fetch($key);
        }

        return $resolver->relation($this, $entity);
    }

    public function named($resolver)
    {
        return new $resolver->named($this);
    }
    
    public function query($resolver, $ignoreEmptyValues = false)
    {
        return $resolver->query(
            $this->adapter('read'), 
            $resolver->mapper(), 
            $this->select($ignoreEmptyValues)
        );
    }

    public function write($resolver)
    {
        return new Write(
            $this->adapter('write'), $resolver->mapper() 
        );
    }
}
