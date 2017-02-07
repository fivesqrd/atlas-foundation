<?php
namespace Canvas\File;

class Named
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
        return $this->_model . '/Named.php';
    }

    public function getClass()
    {
        return 'Named extends \Atlas\Model\Named';
    }

    public function getNamespace()
    {
        return "namespace {$this->_namespace}\\{$this->_model};\n";
    }

    public function getProperties()
    {
        return array(
        );
    } 

    public function getMethods()
    {
        return array(
            $this->_createAll(),
        );
    }

    protected function _createAll()
    {
        return "public function all()"
            . "\n{"
            . "\n\treturn this->_query();"
            . "\n}";
    }
}
