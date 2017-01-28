<?php
namespace Aplication\Model;

class Customer
{

	public function mapper()
	{
		return new Customer\Mapper();
	}
	

	public function query($adapter)
	{
		return new Customer\Query($adapter);
	}
	
}