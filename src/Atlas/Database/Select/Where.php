<?php
namespace Atlast\Database\Select;

class Where
{
    protected $_statements = array();

    protected $_ignoreEmptyValues = false;

    public function __construct($ignoreEmptyValues = false)
    {
        $this->_ignoreEmptyValues = $ignoreEmptyValues;
    }

    public function getSql()
    {
        $sql = null;

        foreach ($this->_statements as $key => $value) {
            array_push($sql, "({$key})");
        }

        return implode(' AND ', $sql);
    }

    public function getBoundParams()
    {
        return array_values($this->_statements);
    }

    public function isIn($name, array $values, $alias = null)
    {
        return $this->add(
            $this->_getTemplate($name, 'in', '(?)', $alias), 
            $values
        );
    }

    public function isNotIn($name, array $values, $alias = null)
    {
        return $this->add(
            $this->_getTemplate($name, 'not in', '(?)', $alias), 
            $values
        );
    }

    public function isNotEqual($name, $value, $alias = null)
    {
        return $this->add(
            $this->_getTemplate($name, '!=', '?', $alias),
            $value
        );
    }

    public function isEqual($name, $value, $alias = null)
    {
        return $this->add(
            $this->_getTemplate($name, '=', '?', $alias), 
            $value
        );
    }
    
    public function isGreaterThan($name, $value, $orEquals = false, $alias = null)
    {
        $op = ($orEquals !== true) ? '>' : '>=';
        return $this->add(
            $this->_getTemplate($name, $op, '?', $alias), 
            $value
        );
    }

    public function isLessThan($name, $value, $orEquals = false, $alias = null)
    {
        $op = ($orEquals !== true) ? '<' : '<=';
        return $this->add(
            $this->_getTemplate($name, $op, '?', $alias), 
            $value
        );
    }
    
    public function isBetween($name, $start, $end, $alias = null)
    {
        return $this->isGreaterThan($name, $start, true, $alias)
            ->isLessThan($name, $end, true, $alias);
    }
    
    public function isLike($name, $value, $alias = null)
    {
        return $this->add(
            $this->_getTemplate($name, 'like', '?', $alias), 
            '%' . $value . '%'
        );
    }

    public function __toString()
    {
        return $this->getSql();
    }

    public function add($statement, $values)
    {
        if ($this->_ignore($statement, $values)) {
            return $this;
        }

        array_push($this->_statements, array(
            $statement => $values
        ));

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

    private function _getTemplate($name, $operator, $placeholder = '?', $alias = null)
    {
        if ($alias == null) {
            $column = $name;
        } else {
            $column = "{$alias}.{$name}";
        }

        return $column . ' ' 
            . $operator . ' ' 
            . $placeholder;
    }
    
}
