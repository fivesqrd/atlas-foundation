<?php
namespace MockModelBarebones\User;

class Collection extends \Atlas\Collection
{

	public function getTargetClass()
	{
		return '\\MockModelBarebones\\User';
	}
	
}