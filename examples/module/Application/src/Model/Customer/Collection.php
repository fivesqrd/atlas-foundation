<?php
namespace Aplication\Model\Customer;

class Collection extends \Atlas\Collection
{

	public function getTargetClass()
	{
		return '\\Aplication\\Model\\Customer';
	}
	
}