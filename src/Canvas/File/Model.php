<?php
namespace Canvas\File;

class Model
{
    protected $_namespace;

    protected $_model;

    public function __construct($namespace, $model)
    {
        $this->_namespace = $namespace;
        $this->_model = $model;
    }

    public function getRelativePath()
    {
        return $this->_model . '.php';
    }

    public function getClass()
    {
        return ucfirst($this->_model);
    }

    public function getNamespace()
    {
        return "namespace {$this->_namespace};\n";
    }

    public function getProperties()
    {
        return array();
    }

    public function getMethods()
    {
        return array();
    }
}
