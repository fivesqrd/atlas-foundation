<?php
namespace Atlas\Database;

class Sql
{
    protected $_adapter;

    public function __construct($adapter)
    {
        $this->_adapter = $adapter;
    }

    public function statement($table, $alias, Sql\Select $select)
    {
        return new Sql\Statement(
            $this->_adapter, $table, $alias, $select
        );
    }

    public function insert($table, $data)
    {
        $this->_execute(
            new Sql\Insert($table, $data)
        );

        return $this->_adapter->lastInsertId();
    }

    public function update($table, $data, Sql\Where $where)
    {
        return $this->_execute(
            new Sql\Update($table, $data, $where)
        );
    }

    public function delete($table, Sql\Where $where)
    {
        return $this->_execute(
            new Sql\Delete($table, $where)
        );
    }

    protected function _execute($sql)
    {
        $statement = $this->_adapter->prepare(
            $sql->assemble()
        );

        return $statement->execute(
            $sql->getBoundValues()
        );
    }
}
