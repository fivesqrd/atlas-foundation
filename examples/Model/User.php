<?php
namespace Model;

class User
{

	public function mapper()
	{
		return new User\Mapper();
	}
	

	public function query()
	{
		return new User\Query(self::mapper());
	}
	
}