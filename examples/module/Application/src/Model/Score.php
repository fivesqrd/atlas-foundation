<?php
namespace Application\Model;

class Score
{
    public static function mapper()
    {
        return new Score\Mapper();
    }

    public static function query($adapter)
    {
        return new Score\Query($adapter);
    }
}
