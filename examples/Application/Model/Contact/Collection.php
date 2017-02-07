<?php
namespace Application\Model\Contact;

class Collection extends \Atlas\Model\Collection
{

	public function getTargetClass()
	{
		return '\\Application\\Model\\Contact';
	}
	
}