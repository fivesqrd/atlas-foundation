<?php

$score = new Model\Score\Entity();

if (!$score->get('_value')) {
    $score->setValue(10);
    $score->set('_timestamp', date('Y-m-d H:i:s'));
}

$write = new Atlas\Database\Write($config);
$write->setMapper(new Model\Score\Mapper());
$write->save($score);
