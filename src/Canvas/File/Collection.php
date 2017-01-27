<?php
namespace Canvas\File;

class Collection
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
        return $this->_model . '/Collection.php';
    }

    public function getClass()
    {
        return 'Collection extends \Atlas\Model\Collection';
    }

    public function getNamespace()
    {
        return "namespace {$this->_namespace}\\{$this->_model};\n";
    }

    public function getProperties()
    {
        return array();
    }

    public function getMethods()
    {
        return array(
            $this->_getTargetClass()
        );
    }

    protected function _getTargetClass()
    {
        $path = '\\' . $this->_namespace . '\\' . $this->_model;
        $class = str_replace('\\','\\\\', $path);

        return "public function targetClass()"
            . "\n{"
            . "\n\treturn '{$class}';"
            . "\n}\n";
    }
}
