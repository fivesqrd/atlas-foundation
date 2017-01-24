<?php
namespace Atlas\Model;

abstract class Mapper
{
    protected $_db;
    
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
            $this->_db = Atom_Db::getInstance()->getAdapter();
        }
    
        return $this->_db;
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
     * @param string $table
     * @param int $primarykey
     * @param string $pkColumn
     * @return Atom_Model
     */
    protected function _fetch($table, $primarykey, $masked = false, $pkColumn = 'id')
    {
        if (empty($primarykey)) {
            throw new Exception('Cannot fetch record from ' . $table . '. No primary key provided');
        }
        
        if ($masked) {
            $maskClass = 'Atom_Mask_' . ucfirst(self::$mask);
            if (class_exists($maskClass)) {
                $mask = new $maskClass();
            } else {
                throw new Exception($maskClass . ' could not be found');
            }
            
            $primarykey = $mask->decode($primarykey);
        }
        
        $select = $this->db()->select()
            ->from($table)
            ->where($pkColumn . ' = ?', $primarykey);
        
        $record = Atom_Cache_Model::getInstance()
            ->fetch($table, $primarykey, $select);
        
        return $this->createObject($record);
    }
    
    /**
     * @param string $table
     * @param array $data
     * @param Atom_Model_Entity $model
     * @param string $pkField
     */
    protected function _save($table, $data, $model, $pkField = 'id')
    {
        if ($model->getId() !== null)
        {
            $this->db()->update($table, $data, $pkField . ' = ' . $this->db()->quote($model->getId()));
            $model->notifyObservers('change');
        } else {
            $this->db()->insert($table, $data);
            $id = $this->db()->lastInsertId();
            $model->setId($id);
            $model->notifyObservers('create');
        }
    
        Atom_Cache_Model::getInstance()
            ->delete($table, $model->getId());
            
        return $model->getId();
    }
    
    /**
     * @param string $table
     * @param Atom_Model_Entity $model
     * @param string $pkField
     */
    protected function _delete($table, $model, $pkField = 'id')
    {
        $this->db()->delete($table,$pkField . ' = ' . $this->db()->quote($model->getId()));
        $model->notifyObservers('delete');
        
        Atom_Cache_Model::getInstance()->delete($table, $model->getId());
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
