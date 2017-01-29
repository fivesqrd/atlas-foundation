<?php
namespace Atlas;

abstract class Query
{
    protected $_select;

    protected $_mapper;
    
    public function __construct($mapper, $select)
    {
        $this->_mapper = $mapper;
        $this->_select = $select;
    }

    protected function _select()
    {
        return $this->_select;
    }

    public function getSql()
    {
        return $this->_select->assemble();
    }

    public function fetch()
    {
        return new Query\Fetch(
            $this->_select->getAdapter(),
            $this->_mapper
        );
    }
}
