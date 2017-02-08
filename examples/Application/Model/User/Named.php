<?php
namespace Application\Model\User;

class Named extends \Atlas\Model\Named
{

    public function all()
    {
        return this->_query();
    }
}