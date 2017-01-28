<?php
namespace Atlas;

abstract class Query
{
    protected $_db;

    protected $_select;
    
    public function __construct($db, $ignoreEmptyValues = false)
    {
        $this->_db = $db;

        $this->_select = new Query\Select(
            $db->select(), 
            $db->getMapper()->getAlias(), 
            $ignoreEmptyValues
        );
    }

    public function fetch()
    {
        return new Query\Fetch(
            $this->_select->getAdapter()
            $this->_db->getMapper()
        );
    }
   
}
