<?php
namespace Atlas\Database;

class Write
{
    protected $_provider;

    protected $_mapper;

    public function __construct($config)
    {
        $this->_provider = Provider::factory($config);
    }

    public function setMapper($object)
    {
        $this->_mapper = $object;
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
            $this->_update($entity);
        } else {
            $this->_insert($entity);
        }

        return $entity->getId();
    }

    protected function _insert($entity)
    {
        $key = $this->_provider->insert(
            $this->_mapper->getTable(), 
            $this->_mapper->getRow($entity)
        )->execute();

        $entity->setId($key);

        $this->_notify($entity, 'create');

        return $key;
    }

    protected function _update($entity)
    {
        $this->_provider->update(
            $this->_mapper->getTable(), 
            $this->_mapper->getRow($entity),
            $this->_where($entity->getId())
        )->execute();

        $this->_notify($entity, 'update');
    }
    
    /**
     * @param string $table
     * @param Atlas_Model_Entity $model
     * @param string $pkField
     */
    public function delete($entity)
    {
        $result = $this->_provider
            ->delete($this->_table, $this->_where($model->getId()))
            ->execute();;

        $model->_notify($entity, 'delete');

        return $result;
    }

    protected function _notify($entity, $action)
    {
        foreach ($entity->getObservers() as $observer) {
            $observer->notify($entity, $action);
        }
    }

    protected function _where($id)
    {
        return array($this->_mapper->getKey() => $id);
    }
}
