<?php
namespace Atlas\Database\Sql;

use Atlas\Database\Exception;

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

    public function __construct($adapter, $table, $alias, Select $select)
    {
        $this->_adapter = $adapter;
        $this->_table = $table;
        $this->_alias = $alias;
        $this->_select = $select;
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
            $what = $this->alias('*');
        }

        return $this->_select->assemble(
            $what, $this->table() . ' AS ' . $this->alias()
        );
    }

    private function escape($name)
    {
        /*
         * Escape any lurking backticks
         */
        $escaped = str_replace("`", "``", $name);

        /*
         * Apply the ligit ticks
         */
        return "`{$escaped}`";
    }

    private function table()
    {
        $name = $this->_table;

        if (empty($name)) {
            throw new \Exception('Identifier may not be empty');
        }

        if (!preg_match('/^(?![0-9])[A-Za-z0-9_]*$/', $name)) {
            throw new Exception(
                'Table may contain only alphanumerics or underscores, and may not begin with a digit'
            );
        }

        return $this->escape($name);
    }

    private function alias($identifier = null)
    {
        if ($identifier && $identifier == '*') {
            return $this->escape($this->_alias) . '.' . $identifier;
        }

        if ($identifier) {
            return $this->escape($this->_alias) . '.' . $this->escape($identifier);
        }

        return $this->escape($this->_alias);
    }
}
