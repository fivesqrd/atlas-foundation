<?php
namespace Atlas\Query;

class Fetch
{
    protected $_provider;

    protected $_select;

    public function __construct($adapter, $mapper)
    {
        $this->_provider = $provider;
        $this->_mapper = $mapper;
    }

    protected function _getAdapter()
    {
        $adapter = clone $this->_adapter;

        return $adpter->distinct()->from(
            array($this->_getAlias() => $this->_getTable())
        );
    }
    
    protected function _getTable()
    {
        return $this->_mapper->getTable();
    }

    protected function _getAlias()
    {
        return $this->_mapper->getAlias();
    }

    /**
     * @return int $count
     */
    public function count()
    {
        $adapter = $this->_getAdapter()
            ->reset(Zend_Db_Select::COLUMNS)
            ->reset(Zend_Db_Select::LIMIT_OFFSET)
            ->reset(Zend_Db_Select::LIMIT_COUNT);
    
        return $adapter->distinct()
            ->from(array($this->_getAlias() => $this->_getTable()),new Zend_Db_Expr('COUNT(distinct ' . $this->_getAlias() . '.id)'))
            ->query()
            ->fetchColumn();
    }

    /**
     * @param string $column
     * @return number $sum
     */
    public function sum($column)
    {
        $adapter = $this->_getAdapter()
            ->reset(Zend_Db_Select::COLUMNS)
            ->reset(Zend_Db_Select::LIMIT_OFFSET)
            ->reset(Zend_Db_Select::LIMIT_COUNT);
    
        $select->distinct()
            ->from(array($this->_getAlias() => $this->_getTable()),new Zend_Db_Expr('SUM(' . $this->_getAlias() . '.' . $column . ')'))
            ->query()
            ->fetchColumn();
    }
    
    /**
     * @param int $currentPage
     * @param int $itemsPerPage
     * @return Atom_Model_Collection
     */
    public function page($currentPage, $itemsPerPage)
    {
        $adapter = $this->_getAdapter()
            ->limitPage($currentPage, $itemsPerPage);

        return $this->_mapper->getCollection(
            $adapter->query()->fetchAll()
        );
    }
    
    /**
     * @return Atom_Model
     */
    public function one()
    {
        return $this->_mapper->getEntity(
            $this->_getAdapter()->query()->fetch()
        );
    }
    
    /**
     * @return Atom_Model_Collection
     */
    public function all()
    {
        return $this->_mapper->getCollection(
            $this->_getAdapter()->query()->fetchAll()
        );
    }
}
