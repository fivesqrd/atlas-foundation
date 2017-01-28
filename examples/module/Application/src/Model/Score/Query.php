<?php
namespace Model\Score;

class Query extends \Atlas\Query
{
    public function metricIs($value)
    {
        $this->_select->isEqual(
            'metric', $value, $this->getAlias()
        );

        return $this;
    }

    public function periodIs($value)
    {
        $this->_select->isEqual(
            'period', $value, $this->getAlias()
        );
            
        return $this;
    }
}
