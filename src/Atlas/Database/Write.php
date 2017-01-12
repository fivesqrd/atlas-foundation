<?php
namespace Atlas\Database;

class Write
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
     * @param string $table
     * @param array $data
     * @param Atlas_Model_Entity $model
     * @param string $pkField
     */
    public function save($entity)
    {
        if ($model->getId() !== null)
        {
            $this->getAdapter()->update(
                $this->_mapper->getTable(), 
                $this->_mapper->getRow($entity), 
                $this->_where($entity->getId())
            );
            $this->_notify($entity, 'update');
        } else {
            $this->getAdapter()->insert(
                $this->_mapper->getTable(),
                $this->_mapper->getRow($entity), 
            );
            $key = $this->getAdapter()->lastInsertId();
            $entity->setId($key);
            $this->_notify($entity, 'create');
        }

        return $entity->getId();
    }

    protected function _notify($entity, $action)
    {
        foreach ($entity->getObservers() as $observer) {
            $observer->notify($entity, $action);
        }
    }

    protected function _where($id)
    {
        return $this->_mapper->getKey() . ' = ' . $this->getAdapter()->quote($id);
    }
    
    /**
     * @param string $table
     * @param Atlas_Model_Entity $model
     * @param string $pkField
     */
    public function delete($entity)
    {
        $this->getAdapter()->delete($this->_table, $this->_where($model->getId()));
        $model->notifyObservers('delete');
    }
}
