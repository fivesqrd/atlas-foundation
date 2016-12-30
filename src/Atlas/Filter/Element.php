<?php
abstract class Atlas_Model_Filter_Element
{
    protected $_values = array();
    
    abstract function getDescription();
    
    abstract function getConditions();
    
    abstract function isValid();
    
    abstract function getParamKeys();

    abstract function getName();
    
    public function getValues()
    {
        return $this->_values;
    }
    
    public function setValue($key, $value)
    {
        $this->_values[$key] = $value;
    }
    
    public function getValue($key)
    {
        if (!array_key_exists($key, $this->_values)) {
            return false;
        }
    
        return $this->_values[$key];
    }
    
    protected function _getDateRangeDescription($start, $end)
    {
        if (strtotime($this->_getStartDate($start)) <= strtotime('2000-01-01 00:00:00')) {
            return 'up to ' . $this->_getEndDate($end);
        }
    
        if ($this->_getEndDate($end) == date("Y-m-d")) {
            return 'since ' . $this->_getStartDate($start);
        }
    
        return $this->_getStartDate($start) . ' to ' . $this->_getEndDate($end);
    }
    
    protected function _getEndDate($value)
    {
        if (empty($value)) {
            return date("Y-m-d");
        }
    
        return $value;
    }
    
    protected function _getStartDate($value)
    {
        if (empty($value)) {
            return '2000-01-01';
        }
    
        return $value;
    }
    
    protected function _getEndDateTime($value)
    {
        if (empty($value)) {
            return date("Y-m-d 23:59:59");
        }
    
        if (strlen($value) == 10) {
            return $value . ' 23:59:59';
        }
    
        return $value;
    }
    
    protected function _getStartDateTime($value)
    {
        if (empty($value)) {
            return '2000-01-01 00:00:00';
        }
    
        if (strlen($value) == 10) {
            return $value . ' 00:00:00';
        }
    
        return $value;
    }
    
    public function __toString()
    {
        return get_class($this);
    }
}
