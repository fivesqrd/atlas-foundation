<?php
abstract class Atlas_Model_Observer implements Atlas_Observer
{
    /**
     * @var Atlas_Model
     */
    protected $_model;
    
    protected $_params = array();
    
    /**
     * Method called for each property that have changed
     * 
     * @param string $property
     * @param mixed $before
     * @param mixed $after
     */
    abstract protected function _changeHook($property, $before, $after);
    
    abstract protected function _deleteHook();
    
    abstract protected function _createHook();
    
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
    }
    
    public function getParam($key)
    {
        if (array_key_exists($key, $this->_params)) {
            return $this->_params[$key];
        }
    }
    
    public function update(Atlas_Observable $subject, $action = 'change')
    {
        $this->_model = $subject;
        $after = $this->_model->toArray();
        switch ($action) {
            case 'change':
                foreach ($subject->diff() as $key => $before) {
                    $this->_changeHook($key, $before, $after[$key]);
                }
                break;
            case 'create':
                $this->_createHook();
                break;
            case 'delete':
                $this->_deleteHook();
                break;
        }
        
    }
}
