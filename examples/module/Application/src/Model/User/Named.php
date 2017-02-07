<?php
namespace Model\User;

class Named extends \Atlas\Named
{
	protected function _factory()
	{
		return new Query($this->_adapter);
	}
}
