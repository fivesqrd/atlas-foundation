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
        return 'Named extends \Atlas\Named';
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
            $this->_createFactory(),
        );
    }

    protected function _createFactory()
    {
        return "protected function _factory()"
            . "\n{"
            . "\n\treturn new Query(\$this->_adapter);"
            . "\n}";
    }
}
