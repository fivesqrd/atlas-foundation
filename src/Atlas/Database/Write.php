<?php
namespace Atlas\Database;

class Write
{
    protected $_sql;

    protected $_mapper;

    public function __construct($sql, $mapper)
    {
        $this->_sql = $sql;
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
        } else {
            $this->insert($entity);
        }
    
        return $entity->getId();
    }

    public function insert($entity)
    {
        $id = $this->_sql->insert(
            $this->_mapper->getTable(), $this->_mapper->extract($entity)
        );

        $entity->setId($id);
        $entity->notify('create');

        return $entity->getId();
    }

    public function update($entity, $column)
    {
        $this->_sql->update(
            $this->_mapper->getTable(), 
            $this->_mapper->extract($entity),
            (new Sql\Where())->isEqual($column, $entity->getId())
        );

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
        $this->_sql->delete(
            $this->_mapper->getTable(),
            (new Sql\Where())->isEqual($column, $entity->getId())
        );

        $entity->notify('delete');

        return $result;
    }
}
