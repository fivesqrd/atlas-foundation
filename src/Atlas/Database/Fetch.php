<?php
namespace Atlas\Database;

class Fetch
{
    /**
     * @var Zend_Db
     */
    protected $_adapter;

    /**
     * @var Atlas\Database\Select
     */
    protected $_select;

    /**
     * @var Atlas\Model\Maper
     */
    protected $_mapper;

    public function __construct($adapter, $mapper, $select)
    {
        $this->_adapter = $adapter;
        $this->_mapper = $mapper;
        $this->_select = $select;
    }

    protected function _getSql()
    {
        $sql = clone $this->_select->getSql();

        return $sql->distinct()->from(
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
        $sql = $this->_getSql()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->reset(\Zend_Db_Select::LIMIT_OFFSET)
            ->reset(\Zend_Db_Select::LIMIT_COUNT);
    
        return $sql->distinct()
            ->from(array($this->_getAlias() => $this->_getTable()),new \Zend_Db_Expr('COUNT(distinct ' . $this->_getAlias() . '.id)'))
            ->query()
            ->fetchColumn();
    }

    /**
     * @param string $column
     * @return number $sum
     */
    public function sum($column)
    {
        $sql = $this->_getSql()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->reset(\Zend_Db_Select::LIMIT_OFFSET)
            ->reset(\Zend_Db_Select::LIMIT_COUNT);
    
        $select->distinct()
            ->from(array($this->_getAlias() => $this->_getTable()),new \Zend_Db_Expr('SUM(' . $this->_getAlias() . '.' . $column . ')'))
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
        $sql = $this->_getSql()
            ->limitPage($currentPage, $itemsPerPage);

        return $this->_mapper->getCollection(
            $sql->query()->fetchAll()
        );
    }
    
    /**
     * @return Atom_Model
     */
    public function one()
    {
        return $this->_mapper->getEntity(
            $this->_getSql()->query()->fetch()
        );
    }
    
    /**
     * @return Atom_Model_Collection
     */
    public function all()
    {
        return $this->_mapper->getCollection(
            $this->_getSql()->query()->fetchAll()
        );
    }
}
