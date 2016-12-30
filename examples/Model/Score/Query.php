<?php
namespace Model\Score;

class Query extends \Atlas\Query
{
    public function metricIs($value)
    {
        return $this->_equals('metric', $value);
    }

    public function periodIs($value)
    {
        return $this->_equals('period', $value);
    }
}
