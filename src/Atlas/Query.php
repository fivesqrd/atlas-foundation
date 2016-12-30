<?php
namespace Atlas;

abstract class Query
{
    /**
     * @var Zend_Db_Select
     */
    protected $_select;
    
    protected $_mapper;
    
    protected $_filters;
    
    protected $_ignoreEmptyValues = false;
    
    abstract protected function _createCollection($rows, Atlas_Model_Mapper $mapper);
    
    public function __construct(Atlas_Model_Mapper $mapper, $ignoreEmptyValues = false)
    {
        $this->_select = $mapper->db()->select();
        $this->_mapper = $mapper;
        $this->_ignoreEmptyValues = $ignoreEmptyValues;
    }

    protected __toString()
    {
        return (string) $this->getSelect();
    }

    protected function _createCollection($rows)
    {
        return $this->_mapper->createCollection($rows);
    }

    protected function _getAlias()
    {
        return $this->_mapper->getAlias();
    }

    protected function _getTable()
    {
        return $this->_mapper->getTable();
    }
    
    protected function _joinModelCreateLog($class)
    {
        if (empty($class)) {
            throw new Exception('Model class is required when using model logs');
        }
        
        if (!$this->_isJoined('mcl')) {
            $on = "mcl.object_key = {$this->getAlias()}.id and mcl.object_type = '{$class}'";
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
            $on = "mdl.object_key = {$this->getAlias()}.id and mdl.object_type = '{$class}";
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
            $on = "mul.object_key = {$this->getAlias()}.id and mul.object_type = '{$class}";
            $this->_select->join(array('mul' => '_model_update_log'), $on, null);
        }
    
        return $this->_select;
    }
    
    protected function _isJoined($alias)
    {
        $parts = $this->_select->getPart(Zend_Db_Select::FROM);
        return (array_key_exists($alias, $parts) || $alias == $this->getAlias());    
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
            $alias = $this->getAlias();
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

    protected function _isIn($name, array $values, $alias = null)
    {
        if (count($values) > 0) {
            $this->_select->where($this->_col($name, 'in', '(?)', $alias), $values);
        }
        return $this;
    }

    protected function _isNotIn($name, array $values, $alias = null)
    {
        if (!$this->_ignore($name, $values)) {
            $this->_select->where($this->_col($name, 'not in', '(?)', $alias), $values);
        }
        return $this;
    }

    protected function _isNotEqual($name, $value, $alias = null)
    {
        if (!$this->_ignore($name, $value)) {
            $this->_select->where($this->_col($name, '!=', '?', $alias), $value);
        }
        return $this;
    }

    protected function _isEqual($name, $value, $alias = null)
    {
        if (!$this->_ignore($name, $value)) {
            $this->_select->where($this->_col($name, '=', '?', $alias), $value);
        }
        return $this;
    }
    
    protected function _isGreaterThan($name, $value, $orEquals = false, $alias = null)
    {
        if (!$this->_ignore($name, $value)) {
            $op = ($orEquals !== true) ? '>' : '>=';
            $this->_select->where($this->_col($name, $op, '?', $alias), $value);
        }
        return $this;
    }

    protected function _isLessThan($name, $value, $orEquals = false, $alias = null)
    {
        if (!$this->_ignore($name, $value)) {
            $op = ($orEquals !== true) ? '<' : '<=';
            $this->_select->where($this->_col($name, $op, '?', $alias), $value);
        }
        return $this;
    }
    
    protected function _isBetween($name, $start, $end, $alias = null)
    {
        return $this->_greaterThan($name, $start, true, $alias)
            ->_lessThan($name, $end, true, $alias);
    }
    
    protected function _isLike($name, $value, $alias = null)
    {
        if (!$this->_ignore($value)) {
            $this->_select->where($this->_col($name, 'like', '?', $alias), '%' . $value . '%');
        }
        return $this;
    }
    
    /**
     * @param string $class
     * @param int $value
     * @return Atlas_Model_Query
     */
    protected function _createdBy($class, $value)
    {
        $this->_joinModelCreateLog($class)->where('mcl.user_id = ?', $value);
        return $this;
    }

    /**
     * @param string $class
     * @param int $value
     * @return Atlas_Model_Query
     */
    protected function _updatedBy($class, $value)
    {
        $this->_joinModelUpdateLog($class)->where('mul.user_id = ?', $value);
        return $this;
    }
    
    /**
     * @param int $value
     * @return Atlas_Model_Query
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
     * @return Atlas_Model_Query
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
     * @return Atlas_Model_Query
     */
    protected function _updatedBetween($class, $start, $end)
    {
        $this->_joinModelUpdateLog($class)
            ->where('mul.timestamp >= ?', $start)
            ->where('mul.timestamp <= ?', $end);
        return $this;
    }
    
    /**
     * @param Atlas_Model_Filter $filter
     * @return Atlas_Model_Query
     */
    public function apply(Atlas_Model_Filter $filter)
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
     * @return Atlas_Model_Query
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
            ->from(array($this->getAlias() => $this->getTable()),new Zend_Db_Expr('COUNT(distinct ' . $this->getAlias() . '.id)'));
        
        return Atlas_Cache_Select::getInstance()
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
            ->from(array($this->getAlias() => $this->getTable()),new Zend_Db_Expr('SUM(' . $this->getAlias() . '.' . $column . ')'));
        
        return Atlas_Cache_Select::getInstance()
            ->fetchColumn($select, null);
    }
    
    /**
     *
     * @param string|array $spec See Zend_Db_Select::order();
     * @link http://framework.zend.com/manual/1.12/en/zend.db.select.html#zend.db.select.building.order
     * @return Atlas_Query
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
        return $select->distinct()->from(array($this->getAlias() => $this->getTable()));
    }
    
    /**
     * @param int $currentPage
     * @param int $itemsPerPage
     * @return Atlas_Model_Collection
     */
    public function fetchByPage($currentPage, $itemsPerPage)
    {
        $select = $this->getSelect()->limitPage($currentPage, $itemsPerPage);
        
        $records = Atlas_Cache_Select::getInstance()
            ->fetchAll($select, array());
        
        return $this->_createCollection($records, $this->_mapper);
    }
    
    /**
     * @return Atlas_Model
     */
    public function fetchOne()
    {
        $record = Atlas_Cache_Select::getInstance()
            ->fetchOne($this->getSelect(), array());
        
        return $this->_mapper->createObject($record);
    }
    
    /**
     * @return Atlas_Model_Collection
     */
    public function fetchAll()
    {
        $records = Atlas_Cache_Select::getInstance()
            ->fetchAll($this->getSelect(), array());
        
        return $this->_createCollection($records, $this->_mapper);
    }
}
