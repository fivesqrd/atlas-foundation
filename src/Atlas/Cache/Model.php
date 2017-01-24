<?php
/** 
 * Singleton representing a cache for storing model objects
 * @author cjb
 * 
 */

class Atlas_Cache_Model
{
    /**
     * @var Zend_Cache_Core
     */
    protected $_storage;
    
    public static $disabled = false;
    
    public function __construct()
    {
        if (extension_loaded('memcached')) {
            $backend = 'libmemcached';
        } else {
            $backend = 'file';
        }
        $this->_storage = Zend_Cache::factory('Core', $backend, array('automatic_serialization' => true));
    }

    public function save($table, $id, $record)
    {
        if (empty($record)) {
            return $record;
        }
        
        $this->_storage->save($record, $this->_generateKey($table, $id));
        
        return $record;
    }
    
    public function get($table, $id)
    {
        if (self::$disabled === true) {
            return false;
        }

        return $this->_storage->load($this->_generateKey($table, $id));
    }
    
    public function delete($table, $id)
    {
        Atlas_Cache_Select::getInstance()->purge($table);
        
        $key = $this->_generateKey($table, $id);

        return $this->_storage->remove($key);
    }
    
    /**
     * Generate a uniqie key
     * 
     * @param string $class
     * @param integer $id
     * @return string $key
     */
    public function _generateKey($table, $id)
    {
        return $table . '_' . $id;
    }
}
?>
