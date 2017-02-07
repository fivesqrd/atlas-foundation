<?php
namespace Application\Model\Company;

class Collection extends \Atlas\Model\Collection
{

	public function getTargetClass()
	{
		return '\\Application\\Model\\Company';
	}
	
}