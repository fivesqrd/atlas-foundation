<?php
namespace Atlas\Database;

class Read
{
    protected $_adapter;

    protected $_mapper;

    public function __construct($config)
    {
        $this->_adapter = new \Zend_Db_Adapter_Pdo_Mysql($config);
    }

    public function setMapper($object)
    {
        $this->_mapper = $object;
    }

    public function getAdapter()
    {
        if (!$this->_adapter) {
            throw new Exception('Database adapter not connected');
        }
        return $this->_adapter;
    }

    /**
     * @return Zend_Db_Select
     */
    public function select()
    {
        return $this->getAdapter()->select();
    }
    
    /**
     *
     * @param int $primarykey
     * @return Atlas\Model\Entity
     */
    public function fetch($key)
    {
        if (empty($key)) {
            throw new Exception('Cannot fetch record from ' . $mapper->getTable() . '. No primary key provided');
        }
        
        $select = $this->getAdapter()->select()
            ->from($this->_mapper->getTable())
            ->where($this->_mapper->getKey() . ' = ?', $key);
        
        $record = Atlas_Cache_Model::getInstance()
            ->fetch($this->_mapper->getTable(), $key, $select);
        
        return $this->_mapper->getObject($record);
    }
}
