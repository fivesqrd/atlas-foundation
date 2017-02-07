<?php
namespace Atlas\Model;

use Atlas\Database as Database;

abstract class Query
{
    /**
     * @var Atlas\Database\Select
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
     * @param int $count
     * @param int $offset
     * @return Atom\Model\Query
     */
    public function limit($count, $offset = null)
    {
        $this->_select()->limit($count, $offset);
        return $this;
    }

    /**
     *
     * @param string|array $spec
     * @return Atom\Model\Query
     */
    public function sort($spec)
    {
        $this->_select()->order($spec);
        return $this;
    }

    /**
     * Get the select object to add to the statement. 
     * Not exposed to user land
     * @return Atlas\Database\Sql\Select
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
        return $this->_select()->assemble();
    }

    /**
     * Get the fetch object to handle the various fetch strategies
     * @return Atlas\Database\Fetch 
     */ 
    public function fetch()
    {
        return new Database\Fetch(
            $this->_adapter, $this->_mapper, $this->_select
        );
    }
}
