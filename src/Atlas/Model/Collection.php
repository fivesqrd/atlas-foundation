<?php
namespace Atlas\Model;

abstract class Collection implements \Iterator, \Countable
{
	protected $_mapper;
	protected $_total = 0;
	protected $_raw = array();
	
	private $_objects = array();
	private $_pointer = 0;
	
	abstract public function getTargetClass();
	
	public function __construct($raw, Mapper $mapper)
	{
		if ($raw && $mapper) {
			$this->_raw = $raw;
			$this->_total = count($raw);
		}
		
		$this->_mapper = $mapper;
	}
	
	public function count()
	{
		return $this->_total;
	}
	
	public function add(Entity $object)
	{
		$class = $this->getTargetClass();
		if (! ($object instanceof $class)) {
			throw new Exception("This is a {$class} collection");
		}
		//TODO: lazy loading
		$this->_objects[$this->_total] = $object;
		$this->_total++;
	}

    public function getRowById($id)
    {
        $ids = array_column($this->_raw, 'id');
        $index = array_search($id, $ids);
        return $this->getRow($index);
    }
	
	public function getRow($no)
	{
        if (!array_key_exists($no, $this->_raw)) {
            return null;
        }
	    
		//TODO: lazy loading
		//if the object already exists return that
		if (!array_key_exists($no, $this->_objects)) {

            $this->_objects[$no] = $this->_mapper->getEntity($this->_raw[$no]);
            
            if (!isset($this->_objects[$no])) {
                throw new Exception('Collection could not create object for class ' . $this->getTargetClass());
            }
        }

		return $this->_objects[$no];
	}
	
	public function rewind()
	{
		$this->_pointer = 0;	
	}
	
	public function current()
	{
		//instanciate new object or return previously instantiated one
		return $this->getRow($this->_pointer);
	}
	
	public function key()
	{
		return $this->_pointer;
	}
	
	public function next()
	{
		$row = $this->getRow($this->_pointer);
		if ($row) $this->_pointer++;
		return $row;
	}
	
	public function valid()
	{
		return $this->current() !== null;
	}
	
	/**
	 * @param string $key
	 * @return array $extract
	 */
	public function extract($key)
	{
	    if (function_exists('array_column')) {
	       return array_column($this->_raw, $key, 'id');
	    } else {
	        $extract = array();
	        foreach ($this->_raw as $row) {
	            if (!array_key_exists($key, $row)) {
	                throw new Exception('Key ' . $key . ' cannot be extracted from row');
	            }
	            $extract[$row['id']] = $row[$key];
	        }
	        
	        return $extract;
	    }
	}
	
	public function toArray()
	{
	    return $this->_raw;
	}
	
	public function first()
	{
	    return $this->getRow(0);
	}
	
	public function delete()
	{
	    foreach ($this as $model) {
	        $model->delete();
	    }
	}

    /**
     * Create a new colleciton containing just items filtered out by the given callback function
     */
    public function filter($callback)
    {
        $rows = array_filter($this->_raw, $callback);

        return new static(array_values($rows), $this->_mapper);
    }

    /**
     * Extract only certain columns from rows
     */
    public function expose($keys)
    {
        $collection = [];

        foreach ($this->_raw as $row) {
            
            $extract = [];
            foreach ($keys as $key) {
                $extract[$key] = array_key_exists($key, $row) ? $row[$key] : null;
            }

            $collection[] = $extract;
        }

        return $collection;
    }
}
?>
