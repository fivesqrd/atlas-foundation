<?php
namespace Atlas\Database\Sql;

class Fetch
{
    /**
     * @var PDO
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

    protected function _getTable()
    {
        return $this->_mapper->getTable();
    }

    protected function _getAlias()
    {
        return $this->_mapper->getAlias();
    }

    public function getStatement($what = null)
    {
        $statement = $this->_adapter->prepare(
            $this->getSql($what)
        ); 

        $statement->execute(
            $this->_select->getBoundValues()
        );

        return $statement;
    }

    public function getSql($what = null)
    {
        if ($what === null) {
            $what = $this->_getAlias() . '.*';
        }

        $from = $this->_getTable() . ' AS ' . $this->_getAlias();

        return $this->_select->assemble($what, $from);
    }

    /**
     * @return int $count
     */
    public function count()
    {
        $what = "COUNT(distinct {$this->_getAlias()}.id)";

        return $this->getStatement($what)->fetchColumn(0);
    }

    /**
     * @param string $column
     * @return number $sum
     */
    public function sum($column)
    {
        $what = "SUM({$this->_getAlias()}.{$column})";

        return $this->getStatement($what)->fetchColumn(0);
    }
    
    /**
     * @return Atom\Model\Entity
     */
    public function one()
    {
        return $this->_mapper->getEntity(
            $this->getStatement()->fetch()
        );
    }
    
    /**
     * @return Atom\Model\Collection
     */
    public function all()
    {
        return $this->_mapper->getCollection(
            $this->getStatement()->fetchAll()
        );
    }
}
