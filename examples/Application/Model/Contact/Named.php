<?php
namespace Application\Model\Contact;

class Named extends \Atlas\Model\Named
{

	public function all()
	{
		return this->_query();
	}
}