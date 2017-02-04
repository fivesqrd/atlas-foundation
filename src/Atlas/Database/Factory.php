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

    public function fetch($resolver, $key)
    {
        return new Sql\Fetch(
            $this->adapter('read'), 
            $resolver->mapper(),
            (new Sql\Select())->isEqual('id', $key)
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
        return $resolver->named($this);
    }
    
    public function query($resolver, $ignoreEmptyValues = false)
    {
        return $resolver->query(
            $this->adapter('read'), 
            $resolver->mapper(), 
            new Sql\Select()
        );
    }

    public function write($resolver)
    {
        return new Write(
            $this->adapter('write'), $resolver->mapper() 
        );
    }
}
