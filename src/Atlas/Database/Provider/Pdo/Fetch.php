<?php
namespace Atlas\Database\Procider\Pdo;

class Fetch
{
    protected $_sql;

    protected $_adapter;

    protected $_params = array();

    public function __construct($adapter, $sql, $params)
    {
        $this->_adapter = $adapter;
        $this->_sql = $sql;
        $this->_params = $params;
    }

    public function one()
    {
        return $this->_adapter
            ->prepare($this->_sql)
            ->execute($this->_params)
            ->fetch(PDO::FETCH_ASSOC);
    }

    public function all()
    {
        return $this->_adapter
            ->prepare($this->_sql)
            ->execute($this->_params)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function column($column = 0)
    {
        return $this->_adapter
            ->prepare($this->_sql)
            ->execute($this->_params)
            ->fetchColumn($column);
    }
}
