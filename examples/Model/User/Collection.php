<?php
namespace Model\User;

class Collection extends \Atlas\Collection
{

	public function targetClass()
	{
		return '\\Model\\User';
	}
	
}