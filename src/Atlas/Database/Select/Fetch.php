<?php
namespace Atlas\Database\Select

class Fetch
{
    protected $_provider;

    protected $_select;

    public function __construct($provider, $mapper, $select)
    {
        $this->_provider = $provider;
        $this->_mapper = $mapper;
        $this->_select = $select;
    }

    public function all()
    {
        $records = $this->_provider->fetch(
            $this->_select->sql()->record($column)
            $this->_select->where()->getBoundParams()
        )->all();

        return $this->_mapper->getCollection($records);
    }

    public function one()
    {
        $record = $this->_provider->fetch(
            $this->_select->sql()->record($column)
            $this->_select->where()->getBoundParams()
        )->one();

        return $this->_mapper->getObject($record);
    }

    public function count()
    {
        return $this->_provider->fetch(
            $this->_select->sql()->count()
            $this->_select->where()->getBoundParams()
        )->column(0);
    }

    public function sum($column)
    {
        return $this->_provider->fetch(
            $this->_select->sql()->sum($column)
            $this->_select->where()->getBoundParams()
        )->column(0);
    }

    public function average($column)
    {
        return $this->_provider->fetch(
            $this->_select->sql()->average($column)
            $this->_select->where()->getBoundParams()
        )->column(0);
    }
}
