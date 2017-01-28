<?php
namespace Model\User;

class Collection extends \Atlas\Collection
{

	public function getTargetClass()
	{
		return '\\Model\\User';
	}
	
}