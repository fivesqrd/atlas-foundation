<?php
namespace Atlas;

abstract class Observer
{
    protected $_params = array();
    
    /**
     * Method called for each property that have changed
     */
    abstract protected function _onUpdate($entity, $delta);
    
    abstract protected function _onDelete($entity);
    
    abstract protected function _onCreate($entity);
    
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
    }
    
    public function getParam($key)
    {
        if (array_key_exists($key, $this->_params)) {
            return $this->_params[$key];
        }
    }
    
    public function notify($entity, $action = 'change')
    {
        switch ($action) {
            case 'update':
                $this->_onUpdate($entity, $this->_getDelta($entity));
                break;
            case 'create':
                $this->_onCreate($entity);
                break;
            case 'delete':
                $this->_onDelete($entity);
                break;
        }
    }

    protected function _getDelta($entity)
    {
        $after = $entity->toArray();
        $delta = array();
        foreach ($entity->diff() as $key => $before) {
            array_push($delta, array(
                'key'    => $key,
                'before' => $before,
                'after'  => $after[$key]
            );
        }

        return $delta;
    }
}
