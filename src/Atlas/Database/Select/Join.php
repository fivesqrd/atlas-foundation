<?php
namespace Atlast\Database\Select;

class Join
{
    protected $_table;

    protected $_alias;

    protected $_columns;

    protected $_type;

    public function __construct($table, $alias = null)
    {
        $this->_table = $table;
        $this->_alias = $alias;
    }

    public function on($value)
    {
        return $this;
    }

    public function inner()
    {
        $this->_type = 'INNER';
        return $this;
    }

    public function left()
    {
        $this->_type = 'LEFT';
        return $this;
    }

    public function assemble()
    {
        $string = null;

        return $this->_getTypeString()
            . "JOIN {$this->_getTableString()}"
            . "ON {$on}";
    }

    public function getAlias()
    {
        if ($this->_alias === null) { 
            return $this->_table;
        }

        return $this->_alias;
    }

    protected function _getTableString()
    {
        return $this->getAlias() . ' ' .  $this->_table . ' ';
    }

    protected function _getTypeString()
    {
        if ($this->_type === null) {
            return null;
        }

        return $this->_type . ' ';
    }
