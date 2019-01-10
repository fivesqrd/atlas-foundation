<?php
namespace Atlas\Database\Sql;

class Statement
{
    /**
     * @var PDO
     */
    protected $_adapter;

    /**
     * @var Atlas\Database\Select
     */
    protected $_select;

    /**
     * @var string
     */
    protected $_table;

    /**
     * @var string
     */
    protected $_alias;

    public function __construct($adapter, $table, $alias, $select)
    {
        $this->_adapter = $adapter;
        $this->_table = $table;
        $this->_alias = $alias;
        $this->_select = $select;
    }

    protected function _table()
    {
        return $this->_table;
    }

    protected function _alias($identifier = null)
    {
        if ($identifier) {
            return $this->_alias . '.' . $identifier;
        }

        return $this->_alias;
    }

    public function execute($what = null)
    {
        $statement = $this->_adapter->prepare(
            $this->assemble($what)
        ); 

        $statement->execute(
            $this->_select->getBoundValues()
        );

        return $statement;
    }

    public function assemble($what = null)
    {
        if ($what === null) {
            $what = $this->_alias('*');
        }

        if (!$this->_table()) {
            throw new Exception('Table name is required');
        }

        $from = $this->_table() . ' AS ' . $this->_alias();

        return $this->_select->assemble($what, $from);
    }
}
