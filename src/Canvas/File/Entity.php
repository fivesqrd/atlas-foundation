<?php
namespace Canvas\File;

class Entity
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
        return $this->_model . '/Entity.php';
    }

    public function getClass()
    {
        return 'Entity extends \Atlas\Model\Entity';
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
        );
    }
}
