<?php
namespace Atlas\Model;

abstract class Query
{
    /**
     * @var Zend_Db_Select
     */
    protected $_select;
    
    protected $_mapper;
    
    protected $_collectionClass;
    
    protected $_filters;
    
    protected $_ignoreEmptyValues = false;
    
    public function __construct($mapper, $ignoreEmptyValues = false)
    {
        $this->_select = $mapper->db()->select();
        $this->_mapper = $mapper;
        $this->_ignoreEmptyValues = $ignoreEmptyValues;
    }

    protected function _getTable()
    {
        return $this->_mapper->getTable();
    }

    protected function _getAlias()
    {
        return $this->_mapper->getAlias();
    }
    
    protected function _createCollection($rows)
    {
        return $this->_mapper->createCollection($rows);
    }
    
    protected function _isJoined($alias)
    {
        $parts = $this->_select->getPart(Zend_Db_Select::FROM);
        return (array_key_exists($alias, $parts) || $alias == $this->_getAlias());    
    }
    
    protected function _isEmpty($value)
    {
        if ($value === '' || $value === null) {
            return true;
        } 
    }
    
    private function _col($name, $operator, $placeholder = '?', $alias = null)
    {
        if ($alias == null) {
            $alias = $this->_getAlias();
        }
        return $alias . '.' . $name . ' ' . $operator . ' ' . $placeholder;
    }
    
    protected function _ignore($name, $value)
    {
        if (!$this->_isEmpty($value)) {
            //don't ignore valid values
            return false;
        }
        
        if ($this->_ignoreEmptyValues === true && $this->_isEmpty($value)) {
            return true;
        }
        
        if ($this->_ignoreEmptyValues === false && $this->_isEmpty($value)) {
            throw new Exception($name . ' value may not be empty');
        }

        return false;
    }

    protected function _in($name, array $values, $alias = null)
    {
        if (count($values) > 0) {
            $this->_select->where($this->_col($name, 'in', '(?)', $alias), $values);
        }
        return $this;
    }

    protected function _notIn($name, array $values, $alias = null)
    {
        if (!$this->_ignore($name, $values)) {
            $this->_select->where($this->_col($name, 'not in', '(?)', $alias), $values);
        }
        return $this;
    }

    protected function _notEquals($name, $value, $alias = null)
    {
        if (!$this->_ignore($name, $value)) {
            $this->_select->where($this->_col($name, '!=', '?', $alias), $value);
        }
        return $this;
    }

    protected function _equals($name, $value, $alias = null)
    {
        if (!$this->_ignore($name, $value)) {
            $this->_select->where($this->_col($name, '=', '?', $alias), $value);
        }
        return $this;
    }
    
    protected function _greaterThan($name, $value, $orEquals = false, $alias = null)
    {
        if (!$this->_ignore($name, $value)) {
            $op = ($orEquals !== true) ? '>' : '>=';
            $this->_select->where($this->_col($name, $op, '?', $alias), $value);
        }
        return $this;
    }

    protected function _lessThan($name, $value, $orEquals = false, $alias = null)
    {
        if (!$this->_ignore($name, $value)) {
            $op = ($orEquals !== true) ? '<' : '<=';
            $this->_select->where($this->_col($name, $op, '?', $alias), $value);
        }
        return $this;
    }
    
    protected function _between($name, $start, $end, $alias = null)
    {
        return $this->_greaterThan($name, $start, true, $alias)
            ->_lessThan($name, $end, true, $alias);
    }
    
    protected function _like($name, $value, $alias = null)
    {
        if (!$this->_ignore($value)) {
            $this->_select->where($this->_col($name, 'like', '?', $alias), '%' . $value . '%');
        }
        return $this;
    }
    
    /**
     * @param int $count
     * @param int $offset
     * @return Atom_Model_Query
     */
    public function limit($count, $offset = null)
    {
        $this->_select->limit($count, $offset);
        return $this;
    }
    
    /**
     * @return int $count
     */
    public function fetchCount()
    {
        $select = clone $this->_select;
        
        $select->reset(Zend_Db_Select::COLUMNS)
            ->reset(Zend_Db_Select::LIMIT_OFFSET)
            ->reset(Zend_Db_Select::LIMIT_COUNT);
    
        return $select->distinct()
            ->from(array($this->_getAlias() => $this->_getTable()),new Zend_Db_Expr('COUNT(distinct ' . $this->_getAlias() . '.id)'))
            ->query()
            ->fetchColumn();
    }

    /**
     * @param string $column
     * @return number $sum
     */
    public function fetchSum($column)
    {
        $select = clone $this->_select;
        
        $select->reset(Zend_Db_Select::COLUMNS)
            ->reset(Zend_Db_Select::LIMIT_OFFSET)
            ->reset(Zend_Db_Select::LIMIT_COUNT);
    
        $select->distinct()
            ->from(array($this->_getAlias() => $this->_getTable()),new Zend_Db_Expr('SUM(' . $this->_getAlias() . '.' . $column . ')'))
            ->query()
            ->fetchColumn();
    }
    
    /**
     *
     * @param string|array $spec See Zend_Db_Select::order();
     * @link http://framework.zend.com/manual/1.12/en/zend.db.select.html#zend.db.select.building.order
     * @return Atom_Query
     */
    public function sort($spec)
    {
        $this->_select->order($spec);
        return $this;
    }

    /**
     * @return Zend_Db_Select $select
     */
    public function getSelect()
    {
        $select = clone $this->_select;
        return $select->distinct()->from(array($this->_getAlias() => $this->_getTable()));
    }
    
    /**
     * @param int $currentPage
     * @param int $itemsPerPage
     * @return Atom_Model_Collection
     */
    public function fetchByPage($currentPage, $itemsPerPage)
    {
        $select = $this->getSelect()->limitPage($currentPage, $itemsPerPage);
        return $this->_createCollection($select->query()->fetchAll());
    }
    
    /**
     * @return Atom_Model
     */
    public function fetchOne()
    {
        return $this->_mapper->createObject($this->getSelect()->query()->fetch());
    }
    
    /**
     * @return Atom_Model_Collection
     */
    public function fetchAll()
    {
        return $this->_createCollection($this->getSelect()->query()->fetchAll());
    }
}
