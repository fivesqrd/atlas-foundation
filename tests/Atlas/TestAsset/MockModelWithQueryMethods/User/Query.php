<?php
namespace MockModelWithQueryMethods\User;

class Query extends \Atlas\Query
{
    public function isEnabled()
    {
        $this->_select()->isEqual('enabled', 1);
        return $this;
    } 
}
