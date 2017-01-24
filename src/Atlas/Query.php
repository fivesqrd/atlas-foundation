<?php
namespace Atlas;

abstract class Query
{
    /**
     * @var Zend_Db_Select
     */
    protected $_select;

    protected $_database;
    
    protected $_mapper;
    
    protected $_filters;
    
    public function __construct(Atlas_Model_Mapper $mapper)
    {
        $this->_database = new Database\Read($config);
        $this->_select = $this->_database->select(); 
        $this->_mapper = $mapper;
        $this->_ignoreEmptyValues = $ignoreEmptyValues;
    }

    protected __toString()
    {
        return (string) $this->getSelect()->getSql();
    }

    protected function _getAlias()
    {
        return $this->_mapper->getAlias();
    }

    protected function _getTable()
    {
        return $this->_mapper->getTable();
    }
    
    protected function _isJoined($alias)
    {
        $parts = $this->_select->getPart(Zend_Db_Select::FROM);
        return (array_key_exists($alias, $parts) || $alias == $this->getAlias());    
    }
    
    /**
     * @param Atlas_Model_Filter $filter
     * @return Atlas_Model_Query
     */
    public function apply(Atlas_Model_Filter $filter)
    {
        foreach ($filter->getAll() as $element) {
            foreach ($element->getConditions() as $function => $params) {
                if (!method_exists($this, $function)) {
                    throw new Exception('Invalid filter condition ' . get_class($this) . '::' . $function);
                }
                call_user_func_array(array($this, $function), $params);
            }
        }
        
        return $this;
    }
    
    /**
     * @param int $count
     * @param int $offset
     * @return Atlas_Model_Query
     */
    public function limit($count, $offset = null)
    {
        $this->_select->limit($count, $offset);
        return $this;
    }
    
    public function order($spec)
    {
        $this->_select->order($spec);
        return $this;
    }
    
    public function fetch()
    {
        return $this->_select->fetch($this->_provider, $this->_mapper);
    }

    public function getSelect()
    {
        return $this->_select
            ->distinct()
            ->from(array($this->getAlias() => $this->getTable()));
    }
}
