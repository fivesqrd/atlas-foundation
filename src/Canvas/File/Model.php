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
        return ucfirst($model);
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
        return array(
            $this->_getMapper(),
            $this->_getQuery(),
        );
    }

    protected function _getMapper()
    {
        return "public function mapper()"
            . "\n{"
            . "\n\treturn new {$this->_model}\\Mapper();"
            . "\n}\n";
    }

    protected function _getQuery()
    {
        return "public function query()"
            . "\n{"
            . "\n\treturn new {$this->_model}\\Query(self::mapper());"
            . "\n}\n";
    }
}
