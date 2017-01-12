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
    protected $_alias;

    protected $_table;

    protected $_key = 'id';

    protected $_map = array();

    protected $_mapReadOnly = array();
    
    abstract public function getObject($row);

    abstract public function getCollection($rows);

    public function getRow($entity)
    {
        return $this->_extract($entity);
    }
    
    public function getAlias()
    {
        return $this->_alias;
    }

    public function getTable()
    {
        return $this->_table;
    }

    public function getKey()
    {
        return $this->_key;
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
