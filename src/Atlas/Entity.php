<?php
namespace Atlas;
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

use Atlas\Exception;

abstract class Entity
{
    protected $_id;
    
    private $__startingValues = array();
    
    private $__observers = array();
    
    public function __construct($properties = array())
    {
        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }
        
        $this->__startingValues = $this->toArray();
    }
    
    public function getId()
    {
        return $this->_id;
    }

    public function setId($value)
    {
        if ($this->_id !== null) {
            throw new Exception ('Model ID may not be overwritten');
        }
        
        $this->_id = $value;
    }

    public function set($property, $value)
    {
        $this->{$property} = $value;
    }

    public function get($property)
    {
        if (!$property) {
            throw new Exception("Property key is required to get value");
        }

        if (!property_exists($this, $property)) {
            throw new Exception("Property '{$property}' does not exist for " . get_class($this));
        }

        return $this->{$property};
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
    public function attach($observers)
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

    public function getObservers()
    {
        return $this->__observers;
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
    
    public function detach(Atlas_Observer $spec)
    {
        foreach ($this->__observers as $key => $observer)
        {
            if ($observer == $spec) unset($this->__observers[$key]);
        }
        return $this;
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
    
    public function __clone()
    {
        $this->__startingValues = $this->toArray();
    }
}