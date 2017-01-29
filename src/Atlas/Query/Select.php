<?php
namespace Atlas\Query;

class Select
{
    protected $_sql;

    protected $_alias;

    protected $_ignoreEmptyValues;

    public function __construct($sql, $alias, $ignoreEmptyValues = false)
    {
        $this->_sql = $sql;
        $this->_alias = $alias;
        $this->_ignoreEmptyValues = $ignoreEmptyValues;
    }

    public function toString()
    {
        return $this->_sql->assemble();
    }

    /**
     * Get the SQL statement creator 
     * @return Zend_Db_Select 
     */ 
    public function getSql()
    {
        return $this->_sql;
    }

    public function isJoined($alias)
    {
        $parts = $this->_sql->getPart(Zend_Db_Select::FROM);

        if (array_key_exists($alias, $parts)) {
            return true;
        } 

        if ($alias == $this->_alias) {
            return true;
        }

        return false;
    }

    public function isIn($name, array $values, $alias = null)
    {
       return $this->addToStack($name, 'in (?)', $values, $alias);
    }

    public function isNotIn($name, array $values, $alias = null)
    {
       return $this->addToStack($name, 'not in (?)', $values, $alias);
    }

    public function isEqual($name, $value, $alias = null)
    {
       return $this->addToStack($name, '=', '?', $value, $alias);
    }

    public function isNotEqual($name, $value, $alias = null)
    {
       return $this->addToStack($name, '!=', '?', $value, $alias);
    }
    
    public function isGreaterThan($name, $value, $orEquals = false, $alias = null)
    {
       $op = ($orEquals !== true) ? '>' : '>=';
       return $this->addToStack($name, $op, '?', $value, $alias);
    }

    public function isLessThan($name, $value, $orEquals = false, $alias = null)
    {
       $op = ($orEquals !== true) ? '<' : '<=';
       return $this->addToStack($name, $op, '?', $value, $alias);
    }
    
    public function isBetween($name, $start, $end, $alias = null)
    {
        return $this->isGreaterThan($name, $start, true, $alias)
            ->isLessThan($name, $end, true, $alias);
    }
    
    public function isLike($name, $value, $alias = null)
    {
       return $this->addToStack($name, 'like', '?', '%' . $value . '%', $alias);
    }

    /**
     * @param int $count
     * @param int $offset
     * @return Atom_Model_Query
     */
    public function limit($count, $offset = null)
    {
        $this->_sql->limit($count, $offset);
        return $this;
    }
    
    /**
     *
     * @param string|array $spec See Zend_Db_Select::order();
     * @link http://framework.zend.com/manual/1.12/en/zend.db.select.html#zend.db.select.building.order
     * @return Atom_Query
     */
    public function sort($spec)
    {
        $this->_sql->order($spec);
        return $this;
    }

    protected function _isEmpty($value)
    {
        if ($value === '' || $value === null) {
            return true;
        } 
    }
    
    private function addToStack($name, $operator, $placeholder = '?', $value, $alias = null)
    {
        if ($this->_ignore($name, $value)) {
            return $this;
        }

        if ($alias == null) {
            $alias = $this->_alias;
        }

        $template = $alias . '.' . $name . ' ' . $operator . ' ' . $placeholder;
        $this->_sql->where($template, $value);

        return $this;
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
}
