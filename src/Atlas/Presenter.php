<?php
namespace Atlas;

class Presenter
{
    protected $_entity;

    public function __construct($entity)
    {
        $this->_entity = $entity;
    }
}
