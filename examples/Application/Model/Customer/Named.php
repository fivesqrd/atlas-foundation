<?php
namespace Application\Model\Customer;

class Named extends \Atlas\Model\Named
{

    public function all()
    {
        return this->_query();
    }
}