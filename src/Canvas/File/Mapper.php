<?php
namespace Canvas\File;

class Mapper
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
        return $this->_model . '/Mapper.php';
    }

    public function getClass()
    {
        return 'Mapper extends \Atlas\Model\Mapper';
    }

    public function getNamespace()
    {
        return "namespace {$this->_namespace}\\{$this->_model};\n";
    }

    public function getProperties()
    {
        return array(
            array('type' => 'protected', 'name' => '_alias', 'value' => 'null'),
            array('type' => 'protected', 'name' => '_table', 'value' => 'null'),
            array('type' => 'protected', 'name' => '_key', 'value' => '\'id\''),
            array('type' => 'protected', 'name' => '_map', 'value' => "array(\n\t\t'_id' => 'id'\n\t)"),
            array('type' => 'protected', 'name' => '_readOnly', 'value' => 'array(\'id\')'),
        );
    } 

    public function getMethods()
    {
        return array(
            $this->_getCreateObject(),
            $this->_getCreateCollection(),
        );
    }

    protected function _getCreateObject()
    {
        return "public function createObject(\$row)"
            . "\n{"
            . "\n\treturn new Entity(\$this->_populate(\$row);"
            . "\n}";
    }

    protected function _getCreateCollection()
    {
        return "public function createCollection(\$rows)"
            . "\n{"
            . "\n\treturn new Collection(\$rows);"
            . "\n}";
    }
}
