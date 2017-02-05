<?php
namespace Atlas\Database;

class Write
{
    protected $_adapter;

    protected $_mapper;

    public function __construct($adapter, $mapper)
    {
        $this->_adapter = $adapter;
        $this->_mapper = $mapper;
    }

    /**
     * @param string $table
     * @param array $data
     * @param Atom_Model_Entity $entity
     * @param string $pkField
     */
    public function save($entity, $column = 'id')
    {
        if ($entity->getId() !== null) {
            $this->update($entity, $column);
        }

        $this->insert($entity, $column);
    
        return $entity->getId();
    }

    public function insert($entity, $column)
    {
        $insert = new Sql\Insert(
            $this->_mapper->getTable(),
            $this->_mapper->extract($entity)
        );

        $result = $this->execute($insert);

        $entity->setId($this->_adapter->lastInsertId());
        $entity->notify('create');

        return $entity->getId();
    }

    public function update($entity, $column)
    {
        $update = new Sql\Update(
            $this->_mapper->getTable(), 
            $this->_mapper->extract($entity),
            (new Sql\Where())->isEqual($column, $entity->getId())
        );

        $result = $this->execute($update);
        $entity->notify('change');
        
        return $entity->getId();
    }
    
    /**
     * @param string $table
     * @param Atom_Model_Entity $entity
     * @param string $pkField
     */
    public function delete($entity, $column = 'id')
    {
        $delete = new Sql\Delete(
            $this->_mapper->getTable(),
            (new Sql\Where())->isEqual($column, $entity->getId())
        );

        $result = $this->execute($delete);
        $entity->notify('delete');

        return $result;
    }

    private function execute($sql)
    {
        $statement = $this->_adapter->prepare(
            $sql->assemble()
        );

        return $statement->execute(
            array_merge(
                $sql->getBoundValues(),
                $sql->where()->getBoundValues()
            )
        );
    }
}
