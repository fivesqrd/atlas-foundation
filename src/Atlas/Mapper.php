<?php
namespace Atlas;
/**
 *
 * Think Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Think Framework
 * license. If you did not receive a copy of the Think Framework license, 
 * please send a note to support@thinkopen.biz so we can mail you a copy 
 * immediately
 * 
 * @copyright  Copyright (c) 2012 Click Science, Think Open Software (Pty) Limted.
 */

abstract class Mapper
{
    protected $_db;
    
    protected $_alias;

    protected $_table;

    protected $_key = 'id';

    protected $_map = array();

    protected $_mapReadOnly = array();
    
    public static $mask = 'base36';
    
    abstract public function createObject($row);
    
    /**
     * @return Zend_Db_Adapter_Pdo_Mysql $db
     */
    public function db()
    {
        if ($this->_db === null) {
            $this->_db = Atlas_Db::getInstance()->getAdapter();
        }
    
        return $this->_db;
    }

    public function getAlias()
    {
        return $this->_alias;
    }

    public function getTable()
    {
        return $this->_table;
    }
    
    /**
     * @return Zend_Db_Select
     */
    public function select()
    {
        return $this->db()->select();
    }
    
    /**
     *
     * @param int $primarykey
     * @return Atlas_Model
     */
    protected function _fetch($key)
    {
        if (empty($key)) {
            throw new Exception('Cannot fetch record from ' . $this->_table . '. No primary key provided');
        }
        
        $select = $this->db()->select()
            ->from($this->_table)
            ->where($this->_key . ' = ?', $key);
        
        $record = Atlas_Cache_Model::getInstance()
            ->fetch($this->_table, $key, $select);
        
        return $this->createObject($record);
    }
    
    /**
     * @param string $table
     * @param array $data
     * @param Atlas_Model_Entity $model
     * @param string $pkField
     */
    protected function _save($data, $model)
    {
        if ($model->getId() !== null)
        {
            $this->db()->update($this->_table, $data, $this->_key . ' = ' . $this->db()->quote($model->getId()));
            $model->notifyObservers('change');
        } else {
            $this->db()->insert($this->_table);
            $key = $this->db()->lastInsertId();
            $model->setId($key);
            $model->notifyObservers('create');
        }
        return $model->getId();
    }
    
    /**
     * @param string $table
     * @param Atlas_Model_Entity $model
     * @param string $pkField
     */
    protected function _delete($model)
    {
        $this->db()->delete($this->_table,$this->_key . ' = ' . $this->db()->quote($model->getId()));
        $model->notifyObservers('delete');
    }
    
    /**
     * @param array $row
     * @return array
     */
    protected function _populate($row)
    {
        $properties = array();
        
        foreach($this->_map as $property => $field) {
            if (!array_key_exists($field, $row)) {
                continue;
            }
            $properties[$property] = $row[$field];
        }
        
        return $properties;
    }
    
    /**
     * @param Atlas_Model_Entity $model
     * @return array
     */
    protected function _extract($model)
    {
        $properties = $model->toArray();
        $data = array();
        
        foreach(array_flip($this->_map) as $field => $property) {
            if ($field == 'id' || in_array($field, $this->_mapReadOnly)) {
                continue;
            }
            $data[$field] = $properties[$property];
        }
        return $data;
    }
}
