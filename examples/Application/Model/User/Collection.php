<?php
namespace Application\Model\User;

class Collection extends \Atlas\Model\Collection
{

	public function getTargetClass()
	{
		return '\\Application\\Model\\User';
	}
	
}