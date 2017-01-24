<?php
namespace Atlas\Database\Provider;

class Pdo
{
    protected $_config;

    protected $_adapter;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function getAdapter()
    {
        if (!$this->_adapter) {
            $this->_adapter = new \PDO($this->_config);
        }

        return $this->_adapter; 
    }

    public function insert($table, $data)
    {
        return new Pdo\Insert($this->getAdapter(), $table, $data);
    }

    public function update($table, $data, $where)
    {
        return new Pdo\Update($this->getAdapter(), $table, $data, $where));
    }

    public function delete($table, $where)
    {
        return new Pdo\Delete($this->getAdapter(), $table, $where));
    }

    public function fetch($sql, $params)
    {
        return new Pdo\Fetch($this->getAdapter(), $sql, $params);
    }
}
