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
            $this->_createGetEntity(),
            $this->_createGetCollection(),
        );
    }

    protected function _createGetEntity()
    {
        return "public function getEntity(\$row)"
            . "\n{"
            . "\n\treturn new Entity(\$this->_populate(\$row));"
            . "\n}";
    }

    protected function _createGetCollection()
    {
        return "public function getCollection(\$rows)"
            . "\n{"
            . "\n\treturn new Collection(\$rows, \$this);"
            . "\n}";
    }
}
