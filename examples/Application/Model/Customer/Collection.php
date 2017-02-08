<?php
namespace Application\Model\Customer;

class Collection extends \Atlas\Model\Collection
{

    public function getTargetClass()
    {
        return '\\Application\\Model\\Customer';
    }
    
}