<?php
/**
 *
 * Think Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Think Framework
 * license. If you did not receive a copy of the Think Framework license, 
 * please send a note to support@thinkopen.biz so we can mail you a copy 
 * immediately
 * 
 * @copyright  Copyright (c) 2012 Click Science, Think Open Software (Pty) Limted.
 */
abstract class Atlas_Model_Entity implements Atlas_Observable
{
    protected $_id;
    
    private $__startingValues = array();
    
    private $__observers = array();
    
    public function getId($masked = false)
    {
        return ($masked) ? $this->_mask($this->_id) : $this->_id;
    }

    protected function _mask($value)
    {
        $maskClass = 'Atlas_Mask_' . ucfirst(Atlas_Model_Mapper::$mask);
        if (class_exists($maskClass)) {
            $mask = new $maskClass();
        } else {
            throw new Exception($maskClass . ' could not be found');
        }
        return $mask->encode($value);
    }
    
    public function setId($value)
    {
        if ($this->_id !== null) {
            throw new Exception ('Model ID may not be overwritten');
        }
        
        $this->_id = $value;
    }
    
    public function __construct($properties = array())
    {
        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }
        
        $this->__startingValues = $this->toArray();
    }
    
    public function diff()
    {
        $diff = array();
        $before = $this->__startingValues;
        $after = $this->toArray();
         
        foreach ($after as $key => $value) {
            //ignore special variables
            if (substr($key,0,2) == '__') {
                continue;
            }
             
            if (!array_key_exists($key,$before)) {
                $diff[$key] = null;
                continue;
            }
             
            if ($value != $before[$key]) {
                $diff[$key] = $before[$key];
            }
        }
         
        foreach ($before as $key => $value) {
            //ignore special variables
            if (substr($key,0,2) == '__') {
                continue;
            }
             
            if (!array_key_exists($key,$after)) {
                $diff[$key] = $value;
            }
        }
         
        return $diff;
    }
    
    /**
     * @param array|Atlas_Model_Entity $observers
     */
    public function attachObserver($observers)
    {
        if (is_array($observers)) {
            foreach ($observers as $observer) {
                $class = get_class($observer);
                $this->__observers[$class] = $observer;
            }
        } else {
            $class = get_class($observer);
            $this->__observers[$class] = $observer;
        }
    }
    
    /**
     * @param string $name
     * @throws Exception
     * @return Atlas_Model_Observer
     */
    public function getObserver($name)
    {
        if (!array_key_exists($name, $this->__observers)) {
            throw new Exception('Observer ' . $name . ' not registered');
        }
        
        return $this->__observers[$name];
    }
    
    public function detachObserver(Atlas_Observer $spec)
    {
        foreach ($this->__observers as $key => $observer)
        {
            if ($observer == $spec) unset($this->__observers[$key]);
        }
        return $this;
    }
    
    public function notifyObservers($action)
    {
        foreach ($this->__observers as $observer) {
            $observer->update($this, $action);
        }
    }
    
    public function toArray()
    {
        return $this->_objectToArray($this);
    }
    
    protected function _objectToArray($object)
    {
        $vars = get_object_vars($object);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $vars[$key] = $this->_objectToArray($value);
            }
        }
    
        return $vars;
    }
    
    public function save()
    {
        $this->mapper()->save($this);
    }
    
    public function delete()
    {
        $this->mapper()->delete($this);
    }
    
    public function __clone()
    {
        $this->__startingValues = $this->toArray();
    }
}
