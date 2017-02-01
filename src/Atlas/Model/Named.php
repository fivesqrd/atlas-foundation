<?php
namespace Atlas\Model;

abstract class Named
{
    /**
     * @var Atlas\Database\Factory
     */
    private $factory;

    /**
     * @var Atlas\Database\Resolver
     */
    private $resolver;

    public function __construct($factory, $resolver)
    {
        $this->factory = $factory;
        $this->resolver = $resolver;
    }

    /**
     * Create new query instance
     * @return Query
     */
    protected function _query($ignoreEmptyValues = false)
    {
        return $this->factory->query($this->resolver, $ignoreEmptyValues);
    }
}
