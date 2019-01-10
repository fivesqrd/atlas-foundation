<?php
namespace Atlas\Database;

class Hydrate
{
    /**
     * @var Atlas\Database\Statement
     */
    protected $_statement;

    /**
     * @var Atlas\Model\Maper
     */
    protected $_mapper;

    public function __construct($mapper, $statement)
    {
        $this->_mapper = $mapper;
        $this->_statement = $statement;
    }

    /**
     * @return int $count
     */
    public function count()
    {
        $what = "COUNT(distinct {$this->_mapper->getAlias()}.id)";

        return $this->_statement->execute($what)->fetchColumn(0);
    }

    /**
     * @param string $column
     * @return number $sum
     */
    public function sum($column)
    {
        $what = "SUM({$this->_mapper->getAlias()}.$column)";

        return $this->_statement->execute($what)->fetchColumn(0);
    }
    
    /**
     * @return array
     */
    public function one()
    {
        return $this->_mapper->getEntity(
            $this->_statement->execute()->fetch()
        );
    }
    
    /**
     * @return Atom\Model\Collection
     */
    public function all()
    {
        return $this->_mapper->getCollection(
            $this->_statement->execute()->fetchAll()
        );
    }
}
