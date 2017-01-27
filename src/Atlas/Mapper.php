<?php
namespace Atlas;

abstract class Mapper
{
    protected $_alias;

    protected $_table;
    
    protected $_map = array();
    
    protected $_mapReadOnly = array();
    
    abstract public function getEntity($row);

    abstract public function getCollection($rows);

    public function getAlias()
    {
        return $this->_alias;
    }

    public function getTable()
    {
        return $this->_table;
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
     * @param Atom_Model_Entity $model
     * @return array
     */
    public function extract($model)
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
