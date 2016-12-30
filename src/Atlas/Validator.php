<?php
abstract class Atlas_Model_Validator
{
    /**
     * @var Atlas_Model
     */
    protected $_model;
    
    /**
     * @var Zend_Filter_Input
     */
    protected $_input;
    
    protected $_filters = array();
    
    protected $_validators = array();
    
    public function __construct($values = array())
    {
        $this->setInput($values);
    }
    
    public function setInput($values)
    {
        $this->_input = new Zend_Filter_Input(
            $this->_filters,
            $this->getValidators(),
            $values
        );
    }
    
    public function getValidators($name = null)
    {
        if (!$name) {
            return $this->_validators;
        }
    
        if (array_key_exists($name, $this->_validators)) {
            return $this->_validators[$name];
        }
    }
    
    /**
     * @return Zend_Filter_Input
     */
    public function input()
    {
        return $this->_input;
    }
}
