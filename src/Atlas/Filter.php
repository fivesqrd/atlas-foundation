<?php
class Atlas_Model_Filter
{
    protected $_elements = array();
    
    protected $_namespace;
    
    /**
     * @param string $namespace
     * @return Atlas_Model_Filter
     */
    public static function getInstance($namespace, $reset = false)
    {
        if (!array_key_exists('Atlas_Model_Filter', $_SESSION)) {
            $_SESSION['Atlas_Model_Filter'] = array();
        }
        
        if ($reset === false && array_key_exists($namespace, $_SESSION['Atlas_Model_Filter']) && $_SESSION['Atlas_Model_Filter'][$namespace]) {
            return unserialize($_SESSION['Atlas_Model_Filter'][$namespace]);
        }

        if ($reset === true) {
            unset($_SESSION['Atlas_Model_Filter'][$namespace]);
        }
        
        return new self($namespace);
    }
    
    public function __construct($namespace)
    {
        $this->_namespace = $namespace;
    }
    
    /**
     * @return Atlas_Model_Filter
     */
    public function reset()
    {
        $this->_elements = array();
        unset($_SESSION['Atlas_Model_Filter'][$this->_namespace]);
        return $this;
    }
    
    /**
     * @return Atlas_Model_Filter
     */
    public function persist()
    {
        $_SESSION['Atlas_Model_Filter'][$this->_namespace] = serialize($this);
        return $this;
    }
    
   /**
    * @param Atlas_Model_Filter_Element
    * @return Atlas_Model_Filter
    */
    public function add(Atlas_Model_Filter_Element $element)
    {
        $key = get_class($element);
        if (!$element->isValid()) {
            return $this;
        }
        
        $this->_elements[$key] = $element;
        return $this;
    }
    
    /**
    * @param array $elements
    * @return Atlas_Model_Filter
    */
    public function addMany(array $elements)
    {
        foreach ($elements as $element) {
            $this->add($element);
        }
        return $this;
    }
    
    /**
     * @param string $key
     * @return Atlas_Model_Filter
     */
    public function remove($key)
    {
        if (array_key_exists($key, $this->_elements)) {
            unset($this->_elements[$key]);
        }
        
        return $this;
    }
    
    /**
     * @param string $key
     * @return Atlas_Model_Filter_Element
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->_elements)) {
            return $this->_elements[$key];
        }
    }
    
    public function getAll()
    {
        return $this->_elements;
    }
    
    /**
     * @param string $key
     * @return mixed
     */
    public function getValue($key)
    {
        $object = $this->get($key);
        if ($object instanceof Atlas_Model_Filter_Element) {
            return $object->getValue();
        }
    }

    /**
     * @param string $key
     * @return string
     */
    public function getDescription($key)
    {
        $object = $this->get($key);
        if ($object instanceof Atlas_Model_Filter_Element) {
            return $object->getDescription();
        }
    }

    /**
     * Match a given value to a selected filter element
     * 
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function isValue($key, $value)
    {
        $object = $this->get($key);
        if ($object instanceof Atlas_Model_Filter_Element) {
            return ($object->getValue() == $value);
        }
        
        return false;
    }
    
    /**
     * Indicates whether the filter has any impact or not
     * @return boolean
     */
    public function isOn()
    {
        return (count($this->_elements) > 0);
    }
}
