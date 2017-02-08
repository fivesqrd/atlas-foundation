<?php
namespace Canvas;

class Writer
{
    protected $_path;

    public function __construct($path)
    {
        $this->_path = $path;
    }

    public function create($file) {
        $filename = $this->_path . '/' . $file->getRelativePath();

        if (file_exists($filename)) {
            throw new \Exception("File {$file->getRelativePath()} already exists, skipping");
        }

        file_put_contents($filename, $this->_getContent($file));
    }

    protected function _getContent($file)
    {
        return "<?php\n{$file->getNamespace()}"
            . "\nclass {$file->getClass()}\n{"
            . $this->_getProperties($file->getProperties())
            . $this->_getMethods($file->getMethods())
            . "\n}";
    }

    protected function _getProperties($properties)
    {
        $string = null;

        foreach ($properties as $property) {
            $string .= "\n    {$property['type']} \${$property['name']} = {$property['value']};\n";
        }

        return $string;
    }

    protected function _getMethods($methods)
    {
        $string = null;

        foreach ($methods as $method) {
            $string .= "\n\n    " . str_replace("\n","\n    ", $method);
        }

        return $string;
    }

}
