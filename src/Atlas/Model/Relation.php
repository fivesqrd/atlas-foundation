<?php
namespace Atlas\Model;

use Atlas\Database\Resolver;
use Atlas\Proxy;

abstract class Relation
{
    private $factory;

    private $entity;

    public function __construct($factory, $entity)
    {
        $this->factory = $factory;
        $this->entity = $entity;
    }

    protected function _getProxy($namespace)
    {
        return new Proxy(
            $this->factory, new Resolver($namespace)
        );
    }

    protected function _getEntity()
    {
        return $this->entity;
    }
}
