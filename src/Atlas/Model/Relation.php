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

    protected function _proxy($namespace)
    {
        return new Proxy(
            $this->factory, new Resolver($namespace)
        );
    }

    protected function _entity()
    {
        return $this->entity;
    }
}
