<?php
namespace Atlas\Model;

abstract class Named
{
    /**
     * @var Atlas\Database\Read
     */
    protected $_router;

    abstract protected function _factory();

    public function __construct($router)
    {
        $this->_router = $router;
    }
}
