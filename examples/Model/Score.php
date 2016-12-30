<?php
namespace Application\Model;

class Score
{
    public static function mapper()
    {
        return new Score\Mapper();
    }

    public static function query()
    {
        return new Score\Query(self::mapper());
    }
}
