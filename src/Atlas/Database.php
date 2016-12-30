<?php
class Atlas_Db
{
    private static $instance;

    protected $_adapter;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function connect($config)
    {
        $this->_adapter = new \Zend_Db_Adapter_Pdo_Mysql($config);
        return $this;
    }

    public function getAdapter()
    {
        if (!$this->_adapter) {
            throw new Exception('Database adapter not connected');
        }
        return $this->_adapter;
    }
}
