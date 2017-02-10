<?php
namespace Application\Model\Contact;

use Application\Model\Customer;

class Query extends \Atlas\Model\Query
{
    public function user()
    {
        return $this->_join(
            'customer_id', array(Customer::class => 'id')
        );
    }
}
