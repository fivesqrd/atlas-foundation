<?php
namespace Atlas\Model;

abstract class Query
{
    protected $_alias;
    
    /**
     * @var Zend_Db_Select
     */
    protected $_select;
    
    protected $_mapper;
    
    protected $_table;
    
    protected $_collectionClass;
    
    protected $_filters;
    
    protected $_ignoreEmptyValues = false;
    
    abstract protected function _createCollection($rows, $mapper);
    
    public function __construct($mapper, $ignoreEmptyValues = false)
    {
        $this->_select = $mapper->db()->select();
        $this->_mapper = $mapper;
        $this->_ignoreEmptyValues = $ignoreEmptyValues;
    }
    
    protected function _joinModelCreateLog($class)
    {
        if (empty($class)) {
            throw new Exception('Model class is required when using model logs');
        }
        
        if (!$this->_isJoined('mcl')) {
            $on = "mcl.object_key = {$this->_alias}.id and mcl.object_type = '{$class}'";
            $this->_select->join(array('mcl' => '_model_create_log'), $on, null);
        }
    
        return $this->_select;
    }
    
    protected function _joinModelDeleteLog($class)
    {
        if (empty($class)) {
            throw new Exception('Model class is required when using model logs');
        }
        
        if (!$this->_isJoined('mdl')) {
            $on = "mdl.object_key = {$this->_alias}.id and mdl.object_type = '{$class}";
            $this->_select->join(array('mdl' => '_model_delete_log'), $on, null);
        }
    
        return $this->_select;
    }
    
    protected function _joinModelUpdateLog($class)
    {
        if (empty($class)) {
            throw new Exception('Model class is required when using model logs');
        }
        
        if (!$this->_isJoined('mul')) {
            $on = "mul.object_key = {$this->_alias}.id and mul.object_type = '{$class}";
            $this->_select->join(array('mul' => '_model_update_log'), $on, null);
        }
    
        return $this->_select;
    }
    
    protected function _isJoined($alias)
    {
        $parts = $this->_select->getPart(Zend_Db_Select::FROM);
        return (array_key_exists($alias, $parts) || $alias == $this->_alias);    
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
            $alias = $this->_alias;
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
     * @param string $class
     * @param int $value
     * @return Atom_Model_Query
     */
    protected function _createdBy($class, $value)
    {
        $this->_joinModelCreateLog($class)->where('mcl.user_id = ?', $value);
        return $this;
    }

    /**
     * @param string $class
     * @param int $value
     * @return Atom_Model_Query
     */
    protected function _updatedBy($class, $value)
    {
        $this->_joinModelUpdateLog($class)->where('mul.user_id = ?', $value);
        return $this;
    }
    
    /**
     * @param int $value
     * @return Atom_Model_Query
     */
    protected function _deletedBy($class, $value)
    {
        $this->_joinModelDeleteLog($class)->where('mdl.user_id = ?', $value);
        return $this;
    }
    
    /**
     * @param string $class
     * @param string $start
     * @param string $end
     * @return Atom_Model_Query
     */
    protected function _createdBetween($class, $start, $end)
    {
        $this->_joinModelCreateLog($class)
            ->where('mcl.timestamp >= ?', $start)
            ->where('mcl.timestamp <= ?', $end);
        return $this;
    }
    
    /**
     * @param string $class
     * @param string $start
     * @param string $end
     * @return Atom_Model_Query
     */
    protected function _updatedBetween($class, $start, $end)
    {
        $this->_joinModelUpdateLog($class)
            ->where('mul.timestamp >= ?', $start)
            ->where('mul.timestamp <= ?', $end);
        return $this;
    }
    
    /**
     * @param Atom_Model_Filter $filter
     * @return Atom_Model_Query
     */
    public function apply($filter)
    {
        foreach ($filter->getAll() as $element) {
            foreach ($element->getConditions() as $function => $params) {
                if (!method_exists($this, $function)) {
                    throw new Exception('Invalid filter condition ' . get_class($this) . '::' . $function);
                }
                call_user_func_array(array($this, $function), $params);
            }
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
    
        $select->distinct()
            ->from(array($this->_alias => $this->_table),new Zend_Db_Expr('COUNT(distinct ' . $this->_alias . '.id)'));
        
        return Atom_Cache_Select::getInstance()
            ->fetchColumn($select, null);
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
            ->from(array($this->_alias => $this->_table),new Zend_Db_Expr('SUM(' . $this->_alias . '.' . $column . ')'));
        
        return Atom_Cache_Select::getInstance()
            ->fetchColumn($select, null);
    }
    
    /**
     *
     * @param string|array $spec See Zend_Db_Select::order();
     * @link http://framework.zend.com/manual/1.12/en/zend.db.select.html#zend.db.select.building.order
     * @return Atom_Query
     */
    public function sortBy($spec)
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
        return $select->distinct()->from(array($this->_alias => $this->_table));
    }
    
    /**
     * @param int $currentPage
     * @param int $itemsPerPage
     * @return Atom_Model_Collection
     */
    public function fetchByPage($currentPage, $itemsPerPage)
    {
        $select = $this->getSelect()->limitPage($currentPage, $itemsPerPage);
        
        $records = Atom_Cache_Select::getInstance()
            ->fetchAll($select, array());
        
        return $this->_createCollection($records, $this->_mapper);
    }
    
    /**
     * @return Atom_Model
     */
    public function fetchOne()
    {
        $record = Atom_Cache_Select::getInstance()
            ->fetchOne($this->getSelect(), array());
        
        return $this->_mapper->createObject($record);
    }
    
    /**
     * @return Atom_Model_Collection
     */
    public function fetchAll()
    {
        $records = Atom_Cache_Select::getInstance()
            ->fetchAll($this->getSelect(), array());
        
        return $this->_createCollection($records, $this->_mapper);
    }
}
