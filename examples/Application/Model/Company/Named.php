<?php
namespace Application\Model\Company;

class Named extends \Atlas\Model\Named
{

	public function all()
	{
		return this->_query();
	}
}