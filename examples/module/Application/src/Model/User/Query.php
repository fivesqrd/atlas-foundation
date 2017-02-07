<?php
namespace Model\User;

class Query extends \Atlas\Query
{
    protected function _joinElement()
    {
        $this->_select()
            ->join('elements', 'e')
            ->on('e.id = t.element_id');

        return $select;
    }

    public function emailAddressIs($value)
    {
        $this->_select->isEqual(
            'email', $value, $this->getAlias()
        );

        return $this;
    }
}
