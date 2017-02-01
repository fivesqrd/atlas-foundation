<?php
namespace Atlas\Database;

class Factory
{
    protected $_config;

    protected $_resolver;

    public function __construct($config, $resolver)
    {
        $this->_config = $config;
        $this->_resolver = $resolver;
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

    public function select($ignoreEmptyValues = false)
    {
        return new Select(
            new \Zend_Db_Select($this->adapter('read')),
            $this->_resolver->mapper()->getAlias(), 
            $ignoreEmptyValues
        );
    }

    public function fetch($key)
    {
        $mapper = $this->_resolver->mapper();
        $select = $this->select($mapper->getAlias())
            ->isEqual('id', $key);

        return new Fetch(
            $this->adapter('read'), $mapper, $select
        );
    }

    public function named()
    {
        return new $this->_resolver->named(
        );
    }
    
    public function query($ignoreEmptyValues = false)
    {
        return $this->_resolver->query(
            $this->adapter('read'), 
            $this->_resolver->mapper(), 
            $this->select($ignoreEmptyValues)
        );
    }

    public function write()
    {
        return new Write(
            $this->adapter('write'), $this->_resolver->mapper() 
        );
    }
}
