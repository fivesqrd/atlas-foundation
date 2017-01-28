<?php
namespace Model\User;

class Query extends \Atlas\Query
{
    public function emailAddressIs($value)
    {
        $this->_select->isEqual(
            'email', $value, $this->getAlias()
        );

        return $this;
    }
}
