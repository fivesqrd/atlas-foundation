<?php
require_once 'Zend/Cache.php';

class Atlas_Cache_Select
{
    public static $tables = array();
    
    public static $disabled = false;

    public static $path;
    
    public static $debug = false;
    
    /**
     * @var \PDO
     */
    protected $_db;
    
    /**
     * @var Zend_Cache_Frontend
     */
    protected $_storage;
    
    protected $_logfile;
    
    private static $instance;
    
    /**
     * Singleton
     * @return Atlas_Cache_Select $cache
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    
    public function __construct()
    {
        if (extension_loaded('memcached')) {
            $backend = 'libmemcached';
        } else {
            $backend = 'file';
        }
        $options = array('automatic_serialization' => true, 'lifetime' => 86400);
        $this->_storage = Zend_Cache::factory('Core', $backend, $options);
        
        $path = (self::$path) ? self::$path : sys_get_temp_dir();
        
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        
        $file = $path . '/Atom-Cache-Select.sqlite3';
        $exists = file_exists($file);
        
        $this->_logfile = $path . '/Atom-Cache-Select.log';
        
        $this->_db = new \PDO('sqlite:' . $file);
        $this->_db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);

        if (!$exists) {
            chmod($file, 0777);
            $this->_createSchema();
        }
    }
    
    protected function _createSchema()
    {
        $this->_db->query('CREATE TABLE dependencies (key varchar(64), name varchar(255), timestamp int(11))');
        $this->_db->query('CREATE UNIQUE INDEX key_name ON dependencies (`key`,`name`)');
    }
    
    protected function _log($message)
    {
        if (self::$debug === false) {
            return;
        }
        file_put_contents($this->_logfile, date("Y-m-d H:i:s") . ': ' . $message . "\n", FILE_APPEND);
    }
    
    public function purge($table)
    {
        $stmt = $this->_db->prepare('SELECT * FROM dependencies WHERE name = :name');
        $stmt->execute(array(':name' => $table));
        $rows = $stmt->fetchAll();
        
        $purged = array();
        foreach ($rows as $row) {
            $this->_storage->remove($row['key']);
            array_push($purged, $row['key']);
        }
        
        $this->_log(count($rows) . " records to purge for table " . $table . ', ' . count($purged) . ' records purged');
        
        return $purged;
    }
    
    /**
     * @todo Save to model cache
     * @param Zend_Db_Select $select
     * @param string $default
     * @return array
     */
    public function fetchOne(Zend_Db_Select $select, $default = null)
    {
        $key = $this->_generateKey($select, 'fetchOne');
        $tables = $this->_getDependencies($select);
        $record = $this->_getRecord($key, $tables);
        
        $verb = ($record === false) ? 'miss' : 'hit';
        $this->_log('fetchOne ' . $verb . ' for ' . $key . ': ' . $select->__toString());
        
        if ($record === false) {
            $record = $select->query()->fetch();
            $this->_save($key, $record, $tables);
            
            /* TODO
             $record = Atlas_Cache_Model::getInstance()
             ->replace($table, $id, $record);
             */
        }
        return empty($record) ? $default : $record;
    }
    
    public function fetchAll(Zend_Db_Select $select, $default = null)
    {
        $key = $this->_generateKey($select, 'fetchAll');
        $tables = $this->_getDependencies($select);
        $records = $this->_getRecord($key, $tables);
        
        $verb = ($records === false) ? 'miss' : 'hit';
        $this->_log('fetchAll ' . $verb . ' for ' . $key . ': ' . $select->__toString());
        
        if ($records === false) {
            $records = $select->query()->fetchAll();
            $this->_save($key, $records, $tables);
        }
        return empty($records) ? $default : $records;
    }

    public function fetchColumn(Zend_Db_Select $select, $default = null)
    {
        $key = $this->_generateKey($select, 'fetchColumn');
        $tables = $this->_getDependencies($select);
        $record = $this->_getRecord($key, $tables);
        
        $verb = ($record === false) ? 'miss' : 'hit';
        $this->_log('fetchColomn ' . $verb . ' for ' . $key . ': ' . $select->__toString());
        
        if ($record === false) {
            $record = $select->query()->fetchColumn();
            $this->_save($key, $record, $tables);
        }
        return empty($record) ? $default : $record;
    }
    
    protected function _getRecord($key, $tables)
    {
        if (self::$disabled === true) {
            return false;
        }
        
        if (!$this->_isAllowed($tables)) {
            return false;
        }
        
        return $this->_storage->load($key);
    }
    
    protected function _generateKey(Zend_Db_Select $select, $prefix = null)
    {
        return sha1(
            serialize($select->getAdapter()->getConfig())
            . serialize($select->getPart(Zend_Db_Select::FROM))
            . serialize($select->getPart(Zend_Db_Select::COLUMNS))
            . serialize($select->getPart(Zend_Db_Select::WHERE))
            . serialize($select->getPart(Zend_Db_Select::ORDER))
            . serialize($select->getPart(Zend_Db_Select::LIMIT_COUNT))
            . serialize($select->getPart(Zend_Db_Select::LIMIT_OFFSET))
            . $prefix
        );
    }
    
    protected function _isAllowed($tables)
    {
        foreach ($tables as $table) {
            if (!in_array($table, self::$tables)) {
                return false;
            }
        }
        
        return true;
    }
    
    protected function _save($key, $record, $dependencies)
    {
        if (empty($record)) {
            return false;
        }
        
        if (self::$disabled === true) {
            return false;
        }
        
        $this->_storage->save($record, $key);
        $this->_map($key, $dependencies);
        $this->_log('Record ' . $key . ' saved with ' . count($dependencies) . ' dependencies');
    }
    
    protected function _getDependencies($select)
    {
        $dependencies = array();
        foreach ($select->getPart(Zend_Db_Select::FROM) as $alias => $join) {
            array_push($dependencies, $join['tableName']);
        }
        return $dependencies;
    }
    
    protected function _map($key, $dependencies)
    {
        if (!$this->_isAllowed($dependencies)) {
            return false;
        }
        
        $stmt = $this->_db->prepare('INSERT OR IGNORE INTO dependencies (key,name,timestamp) values (:key,:table,:time)');
        foreach ($dependencies as $table) {
            $map = array('key' => $key, 'table' => $table, 'time' => time());
            $stmt->execute($map);
        }
    }
}