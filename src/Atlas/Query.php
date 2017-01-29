<?php
namespace Atlas;

abstract class Query
{
    /**
     * @var Atlas\Query\Select
     */
    protected $_select;

    /**
     * @var Atlas\Mapper
     */
    protected $_mapper;

    protected $_adapter;
    
    public function __construct($adapter, $mapper, $select)
    {
        $this->_adapter = $adapter;
        $this->_mapper = $mapper;
        $this->_select = $select;
    }

    /**
     * Get the select object to add to the statement. 
     * Not exposed to user land
     * @return Atlas\Query\Select
     */ 
    protected function _select()
    {
        return $this->_select;
    }

    /**
     * Get the SQL template string for debugging queries 
     * @return string 
     */ 
    public function toString()
    {
        return $this->_select()->toString();
    }

    /**
     * Get the fetch object to handle the various fetch strategies
     * @return Atlas\Query\Fetch 
     */ 
    public function fetch()
    {
        return new Query\Fetch(
            $this->_adapter, $this->_mapper, $this->_select
        );
    }
}
