<?php
namespace Atlas\Database\Sql;

class Where
{
    protected $_stack = array();

    protected $_alias;

    protected $_ignoreEmptyValues = false;

    public function __construct($alias = null, $ignoreEmptyValues = false)
    {
        $this->_alias = $alias;
        $this->_ignoreEmptyValues = $ignoreEmptyValues;
    }

    public function assemble()
    {
        if (count($this->_stack) == 0) {
            return null;
        }

        $clauses = array();

        foreach ($this->_stack as $statement) {
            array_push($clauses, "({$statement['template']})");
        }

        return ' WHERE ' . implode(' AND ', $clauses);
    }

    public function getBoundValues()
    {
        $values = array();

        foreach ($this->_stack as $statement) {
            $values = array_merge(
                $values, $statement['values']
            );
        }

        return $values;
    }

    public function and($statement, $values)
    {
        return $this->addToStack(
            $statement, $values
        );
    }

    public function isIn($name, array $values, $alias = null)
    {
       return $this->parseAndStack(
            $name, 'in', $values, $alias
        );
    }

    public function isNotIn($name, array $values, $alias = null)
    {
       return $this->parseAndStack(
            $name, 'not in', $values, $alias
        );
    }

    public function isEqual($name, $value, $alias = null)
    {
       return $this->parseAndStack(
            $name, '=', $value, $alias
        );
    }

    public function isNotEqual($name, $value, $alias = null)
    {
       return $this->parseAndStack(
            $name, '!=', $value, $alias
        );
    }
    
    public function isGreaterThan($name, $value, $orEquals = false, $alias = null)
    {
       $op = ($orEquals !== true) ? '>' : '>=';
       return $this->parseAndStack($name, $op, $value, $alias);
    }

    public function isLessThan($name, $value, $orEquals = false, $alias = null)
    {
       $op = ($orEquals !== true) ? '<' : '<=';
       return $this->parseAndStack($name, $op, $value, $alias);
    }
    
    public function isBetween($name, $start, $end, $alias = null)
    {
        return $this->isGreaterThan($name, $start, true, $alias)
            ->isLessThan($name, $end, true, $alias);
    }
    
    public function isLike($name, $value, $alias = null)
    {
       return $this->parseAndStack($name, 'like', '%' . $value . '%', $alias);
    }

    public function __toString()
    {
        return $this->getSql();
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

    private function parseAndStack($name, $operator, $values, $alias = null)
    {
        $prefix = null;

        if ($this->_ignore($name, $values)) {
            return $this;
        }

        if ($alias === null && $this->_alias !== null) {
            /* Use default alias if none provided */
            $alias = $this->_alias;
        }

        if ($alias !== null) {
            /* Generate prefix if alias exists */
            $prefix = $alias . '.';
        }

        $placeholder = is_array($values) ? '(?)' : '?';

        $template =  $prefix . $name . ' ' . $operator . ' ' . $placeholder;

        return $this->addToStack($template, $values);
    }

    private function addToStack($template, $values)
    {
        if (!is_array($values)) {
            $values = array($values);
        }

        array_push($this->_stack, array(
            'template' => $template,
            'values'   => $values
        ));

        return $this;
    }
}
