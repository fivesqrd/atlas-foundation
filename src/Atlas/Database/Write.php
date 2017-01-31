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
        $table = $this->_mapper->getTable();
        $data = $this->_mapper->extract($entity);

        if ($entity->getId() !== null)
        {
            $this->_adapter->update($table, $data, $column . ' = ' . $this->_adapter->quote($entity->getId()));
            $entity->notify('change');
        } else {
            $this->_adapter->insert($table, $data);
            $id = $this->_adapter->lastInsertId();
            $entity->setId($id);
            $entity->notify('create');
        }
    
        return $entity->getId();
    }
    
    /**
     * @param string $table
     * @param Atom_Model_Entity $entity
     * @param string $pkField
     */
    public function delete($entity, $column = 'id')
    {
        $this->_adapter->delete(
            $this->_mapper->getTable(), 
            $column . ' = ' . $this->_adapter->quote($entity->getId())
        );

        $entity->notify('delete');
    }
}
