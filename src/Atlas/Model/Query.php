<?php
namespace Atlas\Model;

use Atlas\Database as Database;

abstract class Query
{
    /**
     * @var Atlas\Database\Sql\Select
     */
    protected $_select;

    /**
     * @var Atlas\Model\Mapper
     */
    protected $_mapper;

    protected $_adapter;
    
    public function __construct($adapter, $mapper, $select)
    {
        $this->_adapter = $adapter;
        $this->_mapper = $mapper;
        $this->_select = $select;
    }

    protected function _join($key, array $reference, $type = null)
    {
        $resolver = new Database\Resolver(key($reference));
        $mapper = $resolver->mapper();

        $local = array(
            'alias'  => $this->_mapper->getAlias(), 
            'column' => $key
        ); 

        $foreign = array(
            'table'  => $mapper->getTable(), 
            'alias'  => $mapper->getAlias(),
            'column' => current($reference)
        ); 

        $this->_select()->join($local, $foreign, $type);

        return $resolver->query(
            $this->_adapter, $mapper, $this->_select
        );
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

    protected function _statement()
    {
        return new Database\Sql\Statement(
            $this->_adapter, $this->_mapper->getTable(), $this->_mapper->getAlias(), $this->_select
        );
    }

    /**
     * Get the SQL template string for debugging queries 
     * @return string 
     */ 
    public function toString()
    {
        return $this->_statement()->assemble();
    }

    /**
     * Get the fetch object to handle the various fetch strategies
     * @return Atlas\Database\Hydrate
     */ 
    public function fetch()
    {
        return new Database\Hydrate(
            $this->_mapper, $this->_statement()
        );
    }
}
