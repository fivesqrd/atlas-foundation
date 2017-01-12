<?php
namespace Model\Score;

class Entity extends \Atlas\Entity
{
    protected $_metric;

    protected $_value;

    protected $_period;

    protected $_timestamp;

    public function presenter()
    {
        return new Presenter($this);
    }

    public function relation()
    {
        return new Relation($this);
    }

    public function setValue($value)
    {
        if ($value > 5) {
            throw new \Exception('Value may not be greater than 5');
        }

        $this->_value = $value;
    }

    public function getPeriod($format = null)
    {
        if ($format !== null) {
            return date($format, strtotime($this->_period . '-01'));
        }

        return $this->_period;
    }

    public function getTimestamp($format = null)
    {
        if ($format !== null) {
            return date($format, strtotime($this->_timestamp));
        }

        return $this->_timestamp;
    }
}
