<?php
namespace Atlas\Database;

use Atlas\Cache\Model as Cache;

class Read
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

    public function fetch($select)
    {
        return $select->fetch($this->_provider, $this->_mapper);
    }

    public function fetchByKey($key)
    {
        $fetch = new Fetch\Entity($this->_provider, $this->_mapper);
        return $fetch->execute();
    }
}
