<?php
namespace Atlas\Database;

class Read
{
    protected $_adapter;

    protected $_mapper;

    public function __construct($config, $mapper)
    {
        $this->_adapter = new \Zend_Db_Adapter_Pdo_Mysql($config);
        $this->_mapper = $mapper;
    }
    
    public function getAdapter()
    {
        return $this->_adapter;
    }

    public function getMapper()
    {
        return $this->_mapper;
    }
    
    /**
     *
     * @param string $table
     * @param int $primarykey
     * @param string $pkColumn
     * @return Atom_Model
     */
    public function fetch($key, $column = 'id')
    {
        if (empty($key)) {
            throw new Exception('Cannot fetch record from ' . $this->_mapper->getTable() . '. No primary key provided');
        }
        
        $select = $this->select()
            ->from($this->_mapper->getTable())
            ->where($column . ' = ?', $key);
        
        return $this->_mapper->getEntity($select->query()->fetch());
    }
}
