<?php
require_once('src/Canvas/File/Model.php');
require_once('src/Canvas/File/Entity.php');
require_once('src/Canvas/File/Mapper.php');
require_once('src/Canvas/File/Query.php');
require_once('src/Canvas/File/Collection.php');
require_once('src/Canvas/Writer.php');

if (count($argv) < 4) {
    echo "Usage: {$argv[0]} <namespace> <model> <directory>\n";
    exit;
}

$namespace = $argv[1];
$model = ucfirst($argv[2]);
$path = $argv[3];

$files = array(
    new Canvas\File\Model($namespace, $model),
    new Canvas\File\Entity($namespace, $model),
    new Canvas\File\Mapper($namespace, $model),
    new Canvas\File\Collection($namespace, $model),
    new Canvas\File\Query($namespace, $model),
);

if (!is_dir("{$path}/{$model}")) {
    echo "- Creating model directory in {$path}\n";
    mkdir("{$path}/{$model}");
}

$writer = new Canvas\Writer($path);

foreach ($files as $file) {
    try {
        $writer->create($file);
        echo "- Created {$file->getRelativePath()}\n";
    } catch (Exception $e) {
        echo "- {$e->getMessage()}\n";
    }
}

