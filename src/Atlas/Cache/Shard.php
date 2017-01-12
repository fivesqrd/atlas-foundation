<?php
namespace Atlas\Cache;

class Shard
{
    public static $disabled = false;

    public static $path;
    
    /**
     * @var \PDO
     */
    protected $_db;
    
    /**
     * @var Zend_Cache_Frontend
     */
    protected $_storage;

    protected $_shard;
    
    public function __construct($partition, $shard)
    {
        $this->_shard = $shard;

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
        
        $file = $path . '/Atom-Cache-Shard-' . $partition  . '.sqlite3';
        $exists = file_exists($file);
        
        $this->_db = new \PDO('sqlite:' . $file);
        $this->_db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);

        if (!$exists) {
            chmod($file, 0777);
            $this->_createSchema();
        }
    }
    
    protected function _createSchema()
    {
        $this->_db->query('CREATE TABLE dependencies (key varchar(64), shard int(11), name varchar(255), timestamp int(11))');
        $this->_db->query('CREATE UNIQUE INDEX key_name ON dependencies (`key`,`name`)');
    }
    
    public function purge($table = null)
    {
        if ($table) {
            $stmt = $this->_db->prepare('SELECT * FROM dependencies WHERE name = :name and shard = :shard');
            $stmt->execute(array(':name' => $table, ':shard' => $this->_shard));
        } else {
            $stmt = $this->_db->prepare('SELECT * FROM dependencies WHERE shard = :shard');
            $stmt->execute(array(':shard' => $this->_shard));
        }
        
        $purged = array();
        foreach ($stmt->fetchAll() as $row) {
            $this->_storage->remove($row['key']);
            array_push($purged, $row['key']);
        }
        return $purged;
    }
    
    public function fetchOne(Zend_Db_Select $select, $default = null)
    {
        $key    = $this->_generateKey($select, 'fetchOne');
        $tables = $this->_getDependencies($select);
        $record = $this->_getRecord($key, $tables);
        
        if ($record === false) {
            $record = $select->query()->fetch();
            $this->_save($key, $record, $tables, $this->_shard);
        }
        return empty($record) ? $default : $record;
    }
    
    public function fetchAll(Zend_Db_Select $select, $default = null)
    {
        $key     = $this->_generateKey($select, 'fetchAll');
        $tables  = $this->_getDependencies($select);
        $records = $this->_getRecord($key, $tables);
        
        if ($records === false) {
            $records = $select->query()->fetchAll();
            $this->_save($key, $records, $tables, $this->_shard);
        }
        return empty($records) ? $default : $records;
    }

    public function fetchColumn(Zend_Db_Select $select, $default = null)
    {
        $key    = $this->_generateKey($select, 'fetchColumn');
        $tables = $this->_getDependencies($select);
        $record = $this->_getRecord($key, $tables);
        
        if ($record === false) {
            $record = $select->query()->fetchColumn();
            $this->_save($key, $record, $tables, $this->_shard);
        }
        return empty($record) ? $default : $record;
    }
    
    protected function _getRecord($key)
    {
        if (self::$disabled === true) {
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

    protected function _getDependencies($select)
    {
        $dependencies = array();
        foreach ($select->getPart(Zend_Db_Select::FROM) as $alias => $join) {
            array_push($dependencies, $join['tableName']);
        }
        return $dependencies;
    }
    
    protected function _save($key, $record, $tables, $shard)
    {
        if (self::$disabled === true) {
            return false;
        }

        if ($record === false) {
            //store a null value, when no result was returned
            $record = null;
        }
        
        $this->_storage->save($record, $key);
        $this->_map($key, $tables, $shard);
    }
    
    protected function _map($key, $dependencies, $shard)
    {
        $stmt = $this->_db->prepare('INSERT OR IGNORE INTO dependencies (key,shard,name,timestamp) values (:key,:shard,:name,:time)');
        foreach ($dependencies as $name) {
            $map = array('key' => $key, 'shard' => $shard, 'name' => $name, 'time' => time());
            $stmt->execute($map);
        }
    }
}
