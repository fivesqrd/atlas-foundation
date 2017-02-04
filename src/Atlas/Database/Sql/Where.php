<?php
namespace Atlast\Database\Select;

class Where
{
    protected $_stack = array();

    protected $_ignoreEmptyValues = false;

    public function __construct($ignoreEmptyValues = false)
    {
        $this->_ignoreEmptyValues = $ignoreEmptyValues;
    }

    public function assemble()
    {
        $sql = null;

        foreach (array_keys($this->_stack) as $statement) {
            array_push($sql, "({$statement})");
        }

        return ' WHERE ' . implode(' AND ', $sql);
    }

    public function getBoundValues()
    {
        return array_values($this->_stack);
    }

    public function and($statement, $values)
    {
        array_push($this->_stack, array(
            $statement => $values
        ));

        return $this;
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

    public function __toString()
    {
        return $this->getSql();
    }

    public function addToStack($statement, $values)
    {
        if ($this->_ignore($statement, $values)) {
            return $this;
        }


        return $this;
    }
    
    protected function _isEmpty($value)
    {
        if ($value === '' || $value === null) {
            return true;
        } 
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

    private function addToStack($name, $operator, $placeholder = '?', $value, $alias = null)
    {
        if ($this->_ignore($name, $value)) {
            return $this;
        }

        if ($alias == null) {
            $alias = $this->_alias;
        }

        $template = $alias . '.' . $name . ' ' . $operator . ' ' . $placeholder;

        array_push($this->_stack, array(
            $statement => $values
        ));

        return $this;
    }
}
