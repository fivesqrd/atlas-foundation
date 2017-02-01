<?php
namespace MockModelBarebones;

class User
{

	public function mapper()
	{
		return new User\Mapper();
	}
	

	public function query($adapter)
	{
		return new User\Query($adapter);
	}
	
}