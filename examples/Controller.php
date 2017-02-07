<?php

require_once('../src/Atlas/Exception.php');
require_once('../src/Atlas/Entity.php');
require_once('../src/Atlas/Mapper.php');
require_once('../src/Atlas/Database/Write.php');
require_once('../src/Atlas/Database/Provider.php');
require_once('Model/Score/Entity.php');
require_once('Model/Score/Mapper.php');

$read = new Atlas\Database\Write($config);
$read->setMapper(new Model\Score\Mapper());
$score = $read->fetchByKey($mapper, $key);

$scores = $read->fetch($select)->all();

$score = new Model\Score\Entity();

if (!$score->get('_value')) {
    $score->setValue(3);
    $score->set('_timestamp', date('Y-m-d H:i:s'));
}

$write = new Atlas\Database\Write($config);
$write->setMapper(new Model\Score\Mapper());
$write->save($score);

$query = Model\Score::query()
    ->metricIs('test')
    ->scoreIs(15)
    ->fetch()->all();

$users = Model\User::named($adapter)
    ->getEnabled()
    ->fetch()->all();
