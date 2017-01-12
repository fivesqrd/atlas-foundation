<?php
/** 
 * Singleton representing a cache for storing model objects
 * @author cjb
 * 
 */

class Atlas_Cache_Model
{
    /**
     * 
     * @var Zend_Cache_Core
     */
    protected $_storage;
    
    protected $_namespace;
    
    public static $disabled = false;
    
    private static $instance;
    
    /**
     * Singleton
     * @return Atlas_Cache_Model
     */
    static function getInstance($namespace = null)
    {
        if (!self::$instance) {
            self::$instance = new self($namespace);
        }
        return self::$instance;
    }
    
    public function __construct($namespace = null)
    {
        if (extension_loaded('memcached')) {
            $backend = 'libmemcached';
        } else {
            $backend = 'file';
        }
        $this->_storage = Zend_Cache::factory('Core', $backend, array('automatic_serialization' => true));
    
        $this->_namespace = ($namespace) ? $namespace : sha1(__DIR__);
    }
    
    public function fetch($table, $id, $select, $default = array())
    {
        $key = $this->_generateKey($table, $id);
        
        $record = $this->_get($key);
    
        if ($record === false) {
            $record = $this->_save($key, $select->query()->fetch());
        }
        return empty($record) ? $default : $record;
    }
    
    protected function _save($key, $record)
    {
        if (empty($record)) {
            return $record;
        }
        
        $this->_storage->save($record, $key);
        
        return $record;
    }
    
    protected function _get($key)
    {
        if (self::$disabled === true) {
            return false;
        }
        return $this->_storage->load($key);
    }
    
    /**
     * Replace a model in the registry. If an existing entry 
     * doesn't exist it will be added.
     * 
     * @param string $class
     * @param int $key
     * @param array $row
     */
    public function replace($table, $id, $record)
    {
        Atlas_Cache_Select::getInstance()->purge($table);
        $key = $this->_generateKey($table, $id);
        $record['id'] = $id;
        $this->_storage->save($record, $key);
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
        return $this->_namespace . '_' . $table . '_' . $id;
    }
}
?>
