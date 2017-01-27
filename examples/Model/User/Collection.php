<?php
namespace Model\User;

class Collection extends \Atlas\Model\Collection
{

	public function targetClass()
	{
		return '\\Model\\User';
	}
	
}